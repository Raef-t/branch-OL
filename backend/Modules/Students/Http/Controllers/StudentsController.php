<?php

namespace Modules\Students\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\Students\Http\Requests\StudentsStoreRequest;
use Modules\Students\Http\Requests\StudentsUpdateRequest;
use Modules\Students\Http\Resources\StudentResource;
use Modules\Enrollments\Services\FileUploadService;
use Modules\Students\Http\Resources\StudentProfileResource;
use Modules\Students\Models\Student;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\DB;
use Modules\Attendances\Models\Attendance;
use Modules\Batches\Models\Batch;
use Modules\BatchSubjects\Models\BatchSubject;
use Modules\ClassSchedules\Models\ClassSchedule;
use Modules\ClassSchedules\Services\ClassScheduleTypeService;
use Modules\ExamResults\Http\Resources\ExamResultResource;
use Modules\ExamResults\Models\ExamResult;
use Modules\Exams\Http\Resources\ExamResource;
use Modules\Exams\Models\Exam;
use Modules\InstituteBranches\Models\InstituteBranch;
use Modules\Payments\Http\Resources\PaymentResource;
use Modules\StudentExits\Models\StudentExitLog;
use Modules\Students\Filters\ScheduleFilter;
use Modules\Students\Http\Requests\GetScheduleRequest;
use Modules\Students\Http\Resources\SchedulePeriodsResource;
use Modules\Students\Http\Resources\StudentExamResource;
use Modules\Students\Http\Resources\StudentExamResultResource;
use Modules\Students\Http\Resources\StudentFinancialSummaryResource;
use Modules\Students\Services\ScheduleService;

class StudentsController extends Controller
{
    use SuccessResponseTrait;
    protected FileUploadService $fileService;

    public function __construct(FileUploadService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * @OA\Get(
     *     path="/api/students/profile",
     *     summary="عرض ملف الطالب الحالي بعد تسجيل الدخول",
     *     description="يُرجع بيانات ملف الطالب المرتبط بالمستخدم الحالي بعد التحقق من أنه يحمل دور 'طالب'.",
     *     tags={"Students"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب ملف الطالب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب ملف الطالب بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/StudentResource"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="غير مصادق عليه",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="غير مصرح - المستخدم ليس طالبًا",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="ملف الطالب غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Student profile not found")
     *         )
     *     )
     * )
     */
    public function profile(Request $request)
    {
        /** @var \Modules\Users\Models\User $user */
        $user = $request->user();

        $student = $user->student()
            ->with([
                'instituteBranch',
                'family',
                'branch',
                'bus',
                'status',
                'city',
                'academicRecords.subject',
                'contracts',
                //  'payments',
                'batches',
                'examResults.exam',
                'latestAttendance',
                'latestBatchStudent.batch',
                'school',
            ])
            ->first();

        if (! $student) {
            return response()->json(['message' => 'Student profile not found'], 404);
        }

        return $this->successResponse(
            new StudentProfileResource($student),
            'تم جلب ملف الطالب الكامل بنجاح',
            200
        );
    }

    public function showProfile(Request $request, $id)
    {
        /** @var \Modules\Users\Models\User $user */
        $user = $request->user();

        $student = Student::with([
                'instituteBranch',
                'family',
                'branch',
                'bus',
                'status',
                'city',
                'academicRecords',
                'contracts',
                'batches',
                'examResults.exam',
                'latestAttendance',
                'latestBatchStudent.batch',
                'school',
            ])
            ->find($id);

        if (!$student) {
            return response()->json(['message' => 'الطالب غير موجود'], 404);
        }

        // Authorization logic
        $canAccess = false;
        
        // 1. Admin can access everything
        if ($user->hasRole('admin')) {
            $canAccess = true;
        } 
        // 2. Student can access their own profile
        elseif ($user->student && $user->student->id == $student->id) {
            $canAccess = true;
        } 
        // 3. Family can access their children's profiles
        elseif ($user->family && $user->family->id == $student->family_id) {
            $canAccess = true;
        }

        if (!$canAccess) {
            return response()->json(['message' => 'غير مصرح لك بالوصول لبيانات هذا الطالب.'], 403);
        }

        return $this->successResponse(
            new StudentProfileResource($student),
            'تم جلب ملف الطالب الكامل بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/students",
     *     summary="قائمة الطلاب",
     *     tags={"Students"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="نجاح",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/StudentResource")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $students = Student::with([
            'instituteBranch',
            'family',
            'user',
            'branch',
            'bus',
            'status',
            'city',
            'latestBatchStudent.batch',
            'school'
        ])
        ->where(function ($q) {
            $q->whereDoesntHave('latestBatchStudent')
              ->orWhereHas('latestBatchStudent.batch');
        })
        ->latest()
        ->get();

        return $this->successResponse(
            StudentResource::collection($students),
            'تم جلب الطلاب بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/students",
     *     summary="إنشاء طالب جديد مع دعم رفع الصور",
     *     description="يسمح بإنشاء طالب جديد مع رفع صورة شخصية وصورة بطاقة الهوية كملفات.",
     *     tags={"Students"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="بيانات الطالب مع الملفات (يجب استخدام multipart/form-data)",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"first_name", "last_name", "institute_branch_id", "branch_id"},
     *                 @OA\Property(property="institute_branch_id", type="integer", example=1, description="معرف فرع المعهد"),
     *                 @OA\Property(property="family_id", type="integer", example=123, nullable=true, description="معرف العائلة (اختياري)"),
     *                 @OA\Property(property="user_id", type="integer", example=456, nullable=true, description="معرف المستخدم المرتبط (اختياري)"),
     *                 @OA\Property(property="first_name", type="string", example="خالد", description="الاسم الأول"),
     *                 @OA\Property(property="last_name", type="string", example="أحمد", description="الكنية"),
     *                 @OA\Property(property="date_of_birth", type="string", format="date", example="2010-05-15", nullable=true, description="تاريخ الميلاد"),
     *                 @OA\Property(property="birth_place", type="string", example="دمشق", nullable=true, description="مكان الولادة"),
     *                 @OA\Property(property="branch_id", type="integer", example=2, description="معرف الفرع الدراسي"),
     *                 @OA\Property(property="enrollment_date", type="string", format="date", example="2025-09-01", nullable=true, description="تاريخ التسجيل"),
     *                 @OA\Property(property="start_attendance_date", type="string", format="date", example="2025-09-15", nullable=true, description="تاريخ بدء الحضور"),
     *                 @OA\Property(property="gender", type="string", enum={"male","female"}, example="male", nullable=true, description="الجنس"),
     *                 @OA\Property(property="previous_school_name", type="string", example="مدرسة النجاح", nullable=true, description="المدرسة السابقة"),
     *                 @OA\Property(property="national_id", type="string", example="123456789", nullable=true, description="الرقم الوطني"),
     *                 @OA\Property(property="how_know_institute", type="string", example="توصية", nullable=true, description="كيف عرف بالمعهد؟"),
     *                 @OA\Property(property="bus_id", type="integer", example=5, nullable=true, description="معرف الحافلة"),
     *                 @OA\Property(property="notes", type="string", example="يحتاج دعم إضافي", nullable=true, description="ملاحظات"),
     *                 @OA\Property(property="health_status", type="string", example="سليم", nullable=true, description="الحالة الصحية"),
     *                 @OA\Property(property="psychological_status", type="string", example="طبيعية", nullable=true, description="الحالة النفسية"),
     *                 @OA\Property(property="status_id", type="integer", example=1, nullable=true, description="معرف حالة الطالب"),
     *                 @OA\Property(property="city_id", type="integer", example=3, nullable=true, description="معرف المدينة"),
     *                 @OA\Property(property="qr_code_data", type="string", nullable=true, description="بيانات رمز الاستجابة السريعة"),
     *                 @OA\Property(property="profile_photo", type="string", format="binary", description="صورة شخصية (jpeg, png, jpg, gif - حتى 2MB)"),
     *                 @OA\Property(property="id_card_photo", type="string", format="binary", description="صورة بطاقة الهوية (jpeg, png, jpg, gif, pdf - حتى 2MB)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء الطالب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء الطالب بنجاح"),
     *             @OA\Property(property="data", ref="#/components/schemas/StudentResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="فشل التحقق من البيانات",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     )
     * )
     */
    public function store(StudentsStoreRequest $request)
    {
        $validated = $request->validated();

        // معالجة الصور: نحتفظ بالـ path (وليس الـ URL)
        $photoPath = null;
        $idCardPath = null;

        if ($request->hasFile('profile_photo')) {
            $photoPath = $this->fileService->uploadStudentPhoto($request->file('profile_photo'));
            // لا نستخدم getUrl() هنا!
        }

        if ($request->hasFile('id_card_photo')) {
            $idCardPath = $this->fileService->uploadStudentIdCard($request->file('id_card_photo'));
            // لا نستخدم getUrl() هنا!
        }

        $student = Student::create(array_merge($validated, [
            'profile_photo_url' => $photoPath,    // ✅ نحفظ المسار النسبي
            'id_card_photo_url' => $idCardPath,   // ✅ نحفظ المسار النسبي
        ]));

        $relations = ['instituteBranch', 'family', 'branch', 'bus', 'status', 'city', 'school'];
        if ($student->user_id !== null) {
            $relations[] = 'user';
        }

        $student->load($relations);

        return $this->successResponse(
            new StudentResource($student),
            'تم إنشاء الطالب بنجاح',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/students/{id}",
     *     summary="عرض بيانات طالب محدد",
     *     tags={"Students"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الطالب",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="نجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات الطالب بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="بيانات الطالب مع حالة الحضور اليوم",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="first_name", type="string", example="محمد"),
     *                 @OA\Property(property="last_name", type="string", example="أحمد"),
     *                 @OA\Property(property="full_name", type="string", example="محمد أحمد"),
     *                 @OA\Property(property="date_of_birth", type="string", format="date", example="2010-05-12"),
     *                 @OA\Property(property="gender", type="string", example="male"),
     *                 @OA\Property(property="start_attendance_date", type="string", format="date", example="2025-09-01"),
     *                 @OA\Property(property="batch", type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="الدورة الصيفية"),
     *                     @OA\Property(property="start_date", type="string", format="date", example="2025-06-01"),
     *                     @OA\Property(property="end_date", type="string", format="date", example="2025-08-30")
     *                 ),
     *                 @OA\Property(property="attended_today", type="boolean", example=true, description="هل حضر الطالب اليوم"),
     *                 @OA\Property(property="guardians", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="first_name", type="string", example="سارة"),
     *                         @OA\Property(property="last_name", type="string", example="محمد"),
     *                         @OA\Property(property="national_id", type="string", example="123456789"),
     *                         @OA\Property(property="relationship", type="string", example="أم"),
     *                         @OA\Property(property="phone", type="string", example="0999123456"),
     *                         @OA\Property(property="is_primary_contact", type="boolean", example=true)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الطالب غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الطالب غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $student = Student::with([
                'instituteBranch',
                'family.guardians.contactDetails',
                'family.contactDetails',
                'contactDetails',
                'user',
                'branch',
                'bus',
                'status',
                'city',
                'academicRecords',
                'school'
            ])->find($id);

            if (!$student) {
                return $this->error('الطالب غير موجود', 404);
            }

            return $this->successResponse(
                new StudentResource($student),
                'تم جلب بيانات الطالب بنجاح',
                200
            );
        } catch (\Exception $e) {
            // تسجيل الخطأ في اللوق لتتبعه
            Log::error('خطأ أثناء جلب بيانات الطالب', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // إرجاع استجابة مفهومة للمطور أو العميل
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ غير متوقع أثناء جلب بيانات الطالب',
                'error' => app()->environment('local') ? $e->getMessage() : null, // يظهر الخطأ فقط في بيئة التطوير
            ], 500);
        }
    }

    /**
     * تحديث صور الطالب فقط
     *
     * @OA\Post(
     *     path="/api/students/{id}/photos",
     *     summary="تحديث صور الطالب فقط",
     *     description="يسمح برفع صور جديدة للطالب (الصورة الشخصية وصورة الهوية).",
     *     tags={"Students"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الطالب",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="الصور الجديدة (يجب استخدام multipart/form-data)",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="profile_photo", type="string", format="binary", description="صورة شخصية (jpeg, png, jpg, gif - حتى 2MB)"),
     *                 @OA\Property(property="id_card_photo", type="string", format="binary", description="صورة بطاقة الهوية (jpeg, png, jpg, gif, pdf - حتى 2MB)")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="تم التحديث بنجاح", @OA\JsonContent(ref="#/components/schemas/StudentResource")),
     *     @OA\Response(response=404, description="الطالب غير موجود"),
     *     @OA\Response(response=422, description="فشل التحقق")
     * )
     */
    public function updatePhotos(Request $request, $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return $this->error('الطالب غير موجود', 404);
        }

        // لا نستخدم StudentsUpdateRequest هنا
        $data = [];

        if ($request->hasFile('profile_photo')) {
            $photoPath = $this->fileService->uploadStudentPhoto($request->file('profile_photo'));
            $data['profile_photo_url'] = $photoPath;
        }

        if ($request->hasFile('id_card_photo')) {
            $idCardPath = $this->fileService->uploadStudentIdCard($request->file('id_card_photo'));
            $data['id_card_photo_url'] = $idCardPath;
        }

        if (empty($data)) {
            return $this->error('لم يتم تحميل أي صور', 422);
        }

        $student->update($data);

        $relations = ['instituteBranch', 'family', 'branch', 'bus', 'status', 'city', 'school'];
        if ($student->user_id !== null) {
            $relations[] = 'user';
        }

        $student->load($relations);

        return $this->successResponse(
            new StudentResource($student),
            'تم تحديث صور الطالب بنجاح',
            200
        );
    }

    /**
     * تحديث بيانات الطالب الأساسية (بدون صور)
     *
     * @OA\Put(
     *     path="/api/students/{id}",
     *     summary="تحديث بيانات الطالب الأساسية",
     *     description="يسمح بتحديث بيانات الطالب دون التأثير على الصور.",
     *     tags={"Students"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الطالب",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="بيانات التحديث (JSON)",
     *         @OA\JsonContent(
     *             @OA\Property(property="first_name", type="string", example="خالد"),
     *             @OA\Property(property="last_name", type="string", example="أحمد"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="2010-05-15", nullable=true),
     *             @OA\Property(property="birth_place", type="string", example="دمشق", nullable=true),
     *             @OA\Property(property="branch_id", type="integer", example=2, nullable=true),
     *             @OA\Property(property="enrollment_date", type="string", format="date", example="2025-09-01", nullable=true),
     *             @OA\Property(property="start_attendance_date", type="string", format="date", example="2025-09-15", nullable=true),
     *             @OA\Property(property="gender", type="string", enum={"male","female"}, example="male", nullable=true),
     *             @OA\Property(property="previous_school_name", type="string", example="مدرسة النجاح", nullable=true),
     *             @OA\Property(property="national_id", type="string", example="123456789", nullable=true),
     *             @OA\Property(property="how_know_institute", type="string", example="توصية", nullable=true),
     *             @OA\Property(property="health_status", type="string", example="سليم", nullable=true),
     *             @OA\Property(property="psychological_status", type="string", example="طبيعية", nullable=true),
     *             @OA\Property(property="bus_id", type="integer", example=5, nullable=true),
     *             @OA\Property(property="notes", type="string", example="يحتاج دعم إضافي", nullable=true),
     *             @OA\Property(property="status_id", type="integer", example=1, nullable=true),
     *             @OA\Property(property="city_id", type="integer", example=3, nullable=true),
     *             @OA\Property(property="qr_code_data", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="تم التحديث بنجاح", @OA\JsonContent(ref="#/components/schemas/StudentResource")),
     *     @OA\Response(response=404, description="الطالب غير موجود"),
     *     @OA\Response(response=422, description="فشل التحقق")
     * )
     */
    public function updateBasic(StudentsUpdateRequest $request, $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return $this->error('الطالب غير موجود', 404);
        }

        $validated = $request->validated();

        // استبعاد حقول الصور من التحديث
        unset(
            $validated['profile_photo'],
            $validated['id_card_photo'],
            $validated['profile_photo_url'], // للتأكد
            $validated['id_card_photo_url']
        );

        $student->update($validated);

        $relations = ['instituteBranch', 'family', 'branch', 'bus', 'status', 'city', 'school'];
        if ($student->user_id !== null) {
            $relations[] = 'user';
        }

        $student->load($relations);

        return $this->successResponse(
            new StudentResource($student),
            'تم تحديث بيانات الطالب بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/students/{id}/deletion-report",
     *     summary="تقرير قيود الحذف للطالب",
     *     description="يعيد قائمة بالارتباطات التي تمنع حذف الطالب، مصنفة إلى إدارية وتعليمية.",
     *     tags={"Students"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الطالب",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب التقرير بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="can_delete_standard", type="boolean", example=false),
     *             @OA\Property(property="can_delete_permanent", type="boolean", example=true),
     *             @OA\Property(property="restrictions", type="object",
     *                 @OA\Property(property="administrative", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="educational", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function deletionReport($id)
    {
        $student = Student::find($id);
        if (!$student) {
            return $this->error('الطالب غير موجود', 404);
        }

        $allRestrictions = $student->getDeletionRestrictions();
        
        $administrative = [];
        $educational = [];

        // خريطة لعكس التسميات إلى مفاتيح العلاقات (لأتمتة الفرز)
        $labelToRelation = array_flip($student->getDeletionRestrictedRelations());

        foreach ($allRestrictions as $label) {
            $relation = $labelToRelation[$label] ?? null;
            
            if (in_array($relation, $student->educationalRelations)) {
                $educational[] = $label;
            } else {
                // أي شيء غير مسجل كتعليمي يعتبر إداري/تسجيل
                $administrative[] = $label;
            }
        }

        return $this->successResponse([
            'student_id' => $student->id,
            'full_name' => $student->full_name,
            'can_delete_standard' => empty($allRestrictions),
            'can_delete_permanent' => true, // دائماً مسموح تقنياً ولكن مع تحذير
            'has_educational_records' => !empty($educational),
            'restrictions' => [
                'administrative' => $administrative,
                'educational' => $educational,
            ],
            'family_cleanup' => [
                'is_last_student' => $student->family ? $student->family->students()->count() === 1 : false,
                'family_id' => $student->family_id
            ]
        ], 'تم جلب تقرير قيود الحذف بنجاح');
    }

    /**
     * @OA\Delete(
     *     path="/api/students/{id}",
     *     summary="حذف طالب",
     *     description="يحذف الطالب. إذا تم تمرير permanent=true، سيتم حذف كافة الارتباطات والعائلة إذا كانت فارغة.",
     *     tags={"Students"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الطالب",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="permanent",
     *         in="query",
     *         required=false,
     *         description="حذف نهائي (كاسكيد)",
     *         @OA\Schema(type="boolean", example=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم الحذف بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف الطالب بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الطالب غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الطالب غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request, $id)
    {
        $student = Student::find($id);

        if (!$student) {
            return $this->error('الطالب غير موجود', 404);
        }

        $permanent = $request->query('permanent') === 'true';

        // TODO: مستقبلاً يمكن إضافة تحقق من رول محددة هنا كما طلب المستخدم
        // if ($permanent && !Auth::user()->hasRole('super_admin')) { ... }

        return DB::transaction(function () use ($student, $permanent) {
            $family = $student->family;

            if ($permanent) {
                // حذف كل الارتباطات المحظورة أولاً (Cascade يدوي)
                foreach ($student->getDeletionRestrictedRelations() as $relation => $label) {
                    $relationKey = array_search($label, $student->getDeletionRestrictedRelations());
                    if ($relationKey && method_exists($student, $relationKey)) {
                        $student->{$relationKey}()->delete();
                    }
                }
            }

            $student->delete();

            // تنظيف العائلة إذا كانت فارغة (لا يوجد طلاب آخرون)
            if ($permanent && $family && $family->students()->count() === 0) {
                // حذف أوصياء العائلة وبيانات تواصلهم
                foreach ($family->guardians as $guardian) {
                    $guardian->contactDetails()->delete();
                    $guardian->delete();
                }
                // حذف بيانات تواصل العائلة (مثل رقم الأرضي)
                $family->contactDetails()->delete();
                // حذف سجل العائلة نفسه
                $family->delete();
            }

            return $this->successResponse(
                null,
                $permanent ? 'تم حذف الطالب وكامل بياناته وعائلته بنجاح' : 'تم حذف الطالب بنجاح',
                200
            );
        });
    }

    /**
     * @OA\Get(
     *     path="/api/students/total-students",
     *     summary="عدد الطلاب الكلي مع تفصيل الذكور والإناث",
     *     tags={"Students"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب عدد الطلاب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب عدد الطلاب بنجاح"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="total_students", type="integer", example=4000),
     *                 @OA\Property(property="male_students", type="integer", example=2200),
     *                 @OA\Property(property="female_students", type="integer", example=1800)
     *             )
     *         )
     *     )
     * )
     */
    public function totalStudents()
    {
        $total = Student::count();
        $male = Student::where('gender', 'male')->count();
        $female = Student::where('gender', 'female')->count();

        return $this->successResponse(
            [
                'total_students' => $total,
                'male_students' => $male,
                'female_students' => $female,
            ],
            'تم جلب عدد الطلاب بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/students/details",
     *     summary="قائمة جميع الطلاب بشكل تفصيلي",
     *     description="يعرض جميع الطلاب مع كافة العلاقات (الفرع، الحالة، المدينة، العائلة، الباص، الدفعة الحالية).",
     *     tags={"Students"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب قائمة الطلاب التفصيلية بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب قائمة الطلاب التفصيلية بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/StudentDetailedResource")
     *             )
     *         )
     *     )
     * )
     */
    public function indexDetailed()
    {
        try {

            $students = Student::with([
                'branch',
                'instituteBranch',
                'status',
                'city',
                'bus',
                'family.guardians.contactDetails',
                'latestBatchStudent.batch.batchSubjects.subject',
                'latestActiveEnrollmentContract',
                'school'
            ])
            ->where(function ($q) {
                // عرض الطلاب الذين ليس لديهم شعبة بعد (جدد)
                // أو لديهم شعبة لكنها غير مخفية وغير مؤرشفة
                $q->whereDoesntHave('latestBatchStudent')
                  ->orWhereHas('latestBatchStudent.batch');
            })
            ->orderByDesc('id')
            ->get();

            return $this->successResponse(
                \Modules\Students\Http\Resources\StudentDetailedResource::collection($students),
                'تم جلب قائمة الطلاب التفصيلية بنجاح',
                200
            );

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ أثناء التحميل',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/students/count-per-branch",
     *     summary="عدد الطلاب حسب كل فرع معهد",
     *     description="يعيد قائمة بالفروع مع عدد الطلاب في كل فرع. الطلاب المحتسبين فقط هم الذين لديهم Batch غير مخفي (is_hidden = false).",
     *     tags={"Students"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب عدد الطلاب حسب الفرع بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="branch_id", type="integer", example=1),
     *                     @OA\Property(property="branch_name", type="string", example="فرع دمشق"),
     *                     @OA\Property(property="total_students", type="integer", example=500)
     *                 )
     *             )
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="branch_id",
     *         in="query",
     *         required=false,
     *         description="رقم فرع المعهد (اختياري)",
     *         @OA\Schema(type="integer", example=1)
     *     ),

     *     @OA\Response(
     *         response=500,
     *         description="خطأ في الخادم",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="حدث خطأ أثناء جلب البيانات")
     *         )
     *     )
     * )
     */
    public function countPerBranch(Request $request)
    {
        $branchId = $request->query('branch_id');

        $query = InstituteBranch::query()
            ->select('institute_branches.id', 'institute_branches.name')
            ->withCount([
                'students as total_students' => function ($q) {
                    $q->whereHas('latestBatchStudent.batch', function ($batchQuery) {
                        $batchQuery->where('is_hidden', false);
                    });
                }
            ]);

        // ✅ في حال اختيار "الكل"
        if ($branchId === 'all') {

            $branches = $query->get();
            $grandTotal = $branches->sum('total_students');

            return $this->successResponse(
                [
                    'filter' => 'all',
                    'total_students' => $grandTotal,
                ],
                'تم جلب العدد الإجمالي لجميع الأفرع بنجاح',
                200
            );
        }

        // ✅ في حال تمرير رقم فرع محدد
        if ($branchId) {

            $branch = $query->where('id', $branchId)->first();

            if (!$branch) {
                return response()->json([
                    'status'  => false,
                    'message' => 'الفرع غير موجود',
                ], 404);
            }

            return $this->successResponse(
                [
                    'branch_id'      => $branch->id,
                    'branch_name'    => $branch->name,
                    'total_students' => $branch->total_students,
                ],
                'تم جلب عدد الطلاب للفرع بنجاح',
                200
            );
        }

        // ✅ في حال عدم تمرير أي فلتر (يرجع كل فرع مع عدده)
        $data = $query->get()->map(function ($branch) {
            return [
                'branch_id'      => $branch->id,
                'branch_name'    => $branch->name,
                'total_students' => $branch->total_students,
            ];
        });

        return $this->successResponse(
            $data,
            'تم جلب عدد الطلاب لكل فرع بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/students/{id}/payments",
     *     summary="ملخص الدفعات والعقود للطالب مع الدورة الحالية",
     *     description="يعيد بيانات الطالب، العقود، الدفعات، والدورة الحالية.",
     *     tags={"Students"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الطالب",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب ملخص الدفعات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب ملخص الدفعات بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="student_name", type="string", example="خالد أحمد"),
     *                 @OA\Property(
     *                     property="current_batch",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string", example="دورة الرياضيات 2025")
     *                 ),
     *                 @OA\Property(
     *                     property="contracts_summary",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="contract_id", type="integer"),
     *                         @OA\Property(property="total_amount_usd", type="number", example=1000),
     *                         @OA\Property(property="remaining_amount_usd", type="number", example=200),
     *                         @OA\Property(property="discount_percentage", type="number", example=10),
     *                         @OA\Property(property="discount_amount", type="number", example=100.00)
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="payments",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="receipt_number", type="string", example="REC-001"),
     *                         @OA\Property(property="amount_usd", type="number", example=100),
     *                         @OA\Property(property="payment_date", type="string", format="date", example="2025-10-01")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="الطالب غير موجود",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Student not found"))
     *     )
     * )
     */
    public function paymentsSummary($id)
    {
        // جلب الطالب مع العلاقات المطلوبة
        $student = Student::with([
            'contracts.payments', // عقود + دفعات
            'batchStudents.batch' // كل batchStudents مع batch لجلب الأخير
        ])->find($id);

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }
        // اسم الطالب
        $studentName = trim($student->first_name . ' ' . $student->last_name);

        // الدورة الحالية: جلب آخر batchStudent (الأحدث، مثل latest())
        $latestBatchStudent = $student->batchStudents()->latest('created_at')->first(); // أو حسب الحقل المناسب للترتيب (مثل start_date أو id)

        $currentBatch = $latestBatchStudent ? [
            'id' => $latestBatchStudent->batch->id ?? null,
            'name' => $latestBatchStudent->batch->name ?? 'غير محدد' // أو title أو أي حقل
        ] : ['id' => null, 'name' => 'لا يوجد دورة حالية'];

        // قائمة الدفعات الكاملة (من كل العقود)
        $allPayments = $student->contracts->flatMap(function ($contract) {
            return $contract->payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'receipt_number' => $payment->receipt_number ?? 'غير محدد', // غيّر الحقل لو مختلف
                    'amount_usd' => $payment->amount_usd ?? $payment->amount,
                    'payment_date' => $payment->created_at ? $payment->created_at->format('Y-m-d') : null,
                ];
            });
        });

        // ملخص العقود (لكل عقد: كامل، متبقي، حسم)
        $contractsSummary = $student->contracts->map(function ($contract) {
            $totalAmount = $contract->final_amount_usd ?? $contract->total_amount_usd; // حسب الـ model
            $paidAmount = $contract->paid_amount_usd ?? 0;
            $remainingAmount = $totalAmount - $paidAmount;

            return [
                'contract_id' => $contract->id,
                'total_amount_usd' => $totalAmount,
                'remaining_amount_usd' => $remainingAmount,
                'discount_percentage' => $contract->discount_percentage ?? 0,
                'discount_amount' => $contract->discount_amount ?? 0
            ];
        });

        // هيكل الرد المنظم
        $data = [
            'student_name' => $studentName,
            'current_batch' => $currentBatch,
            'contracts_summary' => $contractsSummary,
            'payments' => $allPayments
        ];

        return $this->successResponse(
            $data,
            'تم جلب ملخص الدفعات بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/students/{id}/details",
     *     summary="بيانات طالب واحد بشكل تفصيلي",
     *     description="يعرض جميع بيانات الطالب مع كافة العلاقات",
     *     tags={"Students"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الطالب",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات الطالب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات الطالب بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/StudentDetailedResource"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="الطالب غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الطالب غير موجود")
     *         )
     *     )
     * )
     */
    public function showStudentDetailed($id)
    {
        try {
            $student = Student::with([
                'branch',
                'instituteBranch',
                'status',
                'city',
                'bus',
                'academicRecords',
                'contactDetails', // وسائل التواصل الشخصية للطالب
                'family.contactDetails', // وسائل تواصل العائلة (مثل الأرضي)
                'family.guardians.contactDetails', // وسائل تواصل الأوصياء
                'latestBatchStudent.batch',
                'latestActiveEnrollmentContract',
            ])->find($id);

            if (!$student) {
                return $this->error('الطالب غير موجود', 404);
            }

            return $this->successResponse(
                new \Modules\Students\Http\Resources\StudentDetailedResource($student),
                'تم جلب بيانات الطالب بنجاح'
            );
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/students/{id}/exams",
     *     summary="جلب امتحانات الطالب مقسمة حسب الدفعات",
     *     description="يعيد قائمة بالامتحانات المتاحة للطالب مقسمة حسب الدفعات، مع تفاصيل الامتحانات والمواد والمدرسين المسؤولين.",
     *     tags={"Students"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الطالب",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب امتحانات الطالب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب امتحانات الطالب بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="batch_id", type="integer", example=1),
     *                     @OA\Property(property="batch_name", type="string", example="دورة الرياضيات 2025"),
     *                     @OA\Property(
     *                         property="exams",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="exam_id", type="integer", example=1),
     *                             @OA\Property(property="exam_name", type="string", example="امتحان الرياضيات الأول"),
     *                             @OA\Property(property="exam_date", type="string", format="date", example="2025-11-15"),
     *                             @OA\Property(property="total_marks", type="integer", example=100),
     *                             @OA\Property(property="passing_marks", type="integer", example=60),
     *                             @OA\Property(property="subject_id", type="integer", example=1),
     *                             @OA\Property(property="subject_name", type="string", example="الرياضيات"),
     *                             @OA\Property(property="instructor_id", type="integer", example=1),
     *                             @OA\Property(property="instructor_name", type="string", example="أحمد محمد")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الطالب غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الطالب غير موجود")
     *         )
     *     )
     * )
     */
    public function getExamsByStudent($id)
    {
        $student = Student::with([
            'batchStudents.batch.batchSubjects.exams'
        ])->find($id);

        if (!$student) {
            return $this->error('الطالب غير موجود', 404);
        }

        $examsByBatch = $student->batchStudents->map(function ($batchStudent) {
            $batch = $batchStudent->batch;

            $exams = $batch->batchSubjects->flatMap(function ($batchSubject) {
                return $batchSubject->exams->map(function ($exam) use ($batchSubject) {
                    return [
                        'exam_id' => $exam->id,
                        'exam_name' => $exam->name,
                        'exam_date' => $exam->exam_date ? $exam->exam_date->format('Y-m-d') : null,
                        'total_marks' => $exam->total_marks,
                        'passing_marks' => $exam->passing_marks,
                        'subject_id' => $batchSubject->subject_id,
                        'subject_name' => $batchSubject->subject->name ?? 'غير محدد',
                        'instructor_id' => $batchSubject->employee_id,
                        'instructor_name' => $batchSubject->Employees->name ?? 'غير محدد'
                    ];
                });
            });

            return [
                'batch_id' => $batch->id,
                'batch_name' => $batch->name,
                'exams' => $exams
            ];
        });

        return $this->successResponse(
            $examsByBatch,
            'تم جلب امتحانات الطالب بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/students/{id}/financial-summary",
     *     summary="جلب البيانات المالية للطالب",
     *     description="يعطي العقد النشط للطالب، المبلغ الكلي، المبلغ المدفوع، المبلغ المتبقي، جميع الدفعات، والأقساط المعلقة فقط",
     *     operationId="getStudentFinancialSummary",
     *     tags={"Students"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="معرف الطالب",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب البيانات المالية للطالب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="تم جلب البيانات المالية للطالب بنجاح"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="student_id", type="integer", example=1),
     *                 @OA\Property(property="full_name", type="string", example="أحمد محمد"),
     *                 @OA\Property(property="enrollment_contract", type="object",
     *                     @OA\Property(property="contract_id", type="integer", example=5),
     *                     @OA\Property(property="total_amount_usd", type="number", format="float", example=1000),
     *                     @OA\Property(property="paid_amount_usd", type="number", format="float", example=400),
     *                     @OA\Property(property="remaining_amount_usd", type="number", format="float", example=600)
     *                 ),
     *                 @OA\Property(property="payments", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=10),
     *                         @OA\Property(property="receipt_number", type="string", example="R-001"),
     *                         @OA\Property(property="amount_usd", type="number", format="float", example=200),
     *                         @OA\Property(property="paid_date", type="string", format="date", example="2025-12-28"),
     *                         @OA\Property(property="note", type="string", example="دفعة أولى")
     *                     )
     *                 ),
     *                 @OA\Property(property="pending_installments", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=3),
     *                         @OA\Property(property="installment_number", type="integer", example=2),
     *                         @OA\Property(property="due_date", type="string", format="date", example="2025-12-31"),
     *                         @OA\Property(property="planned_amount_usd", type="number", format="float", example=300),
     *                         @OA\Property(property="paid_amount_usd", type="number", format="float", example=100),
     *                         @OA\Property(property="remaining_amount_usd", type="number", format="float", example=200),
     *                         @OA\Property(property="status", type="string", example="pending")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الطالب أو العقد النشط غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Student not found or no active contract.")
     *         )
     *     )
     * )
     */
    public function financialSummary($id)
    {
        $student = Student::with([
            'latestActiveEnrollmentContract.payments',
            'latestActiveEnrollmentContract.paymentInstallments' => function ($query) {
                $query->where('status', 'pending'); // فقط الأقساط المعلقة
            }
        ])->find($id);

        if (!$student) {
            return $this->error('الطالب غير موجود', 404);
        }

        $data = new StudentFinancialSummaryResource($student);

        return $this->successResponse(
            $data,
            'تم جلب البيانات المالية للطالب بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/students/{id}/latest-payment",
     *     summary="جلب آخر دفعة للطالب",
     *     description="يعطي آخر دفعة للطالب حسب العقد النشط. إذا لم يكن لديه دفعات ترجع رسالة واضحة.",
     *     operationId="getStudentLatestPayment",
     *     tags={"Students"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="معرف الطالب",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب آخر دفعة للطالب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="تم جلب آخر دفعة للطالب بنجاح"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=15),
     *                 @OA\Property(property="receipt_number", type="string", example="R-010"),
     *                 @OA\Property(property="amount_usd", type="number", format="float", example=250),
     *                 @OA\Property(property="amount_syp", type="number", format="float", example=625000),
     *                 @OA\Property(property="exchange_rate_at_payment", type="number", format="float", example=2500),
     *                 @OA\Property(property="currency", type="string", example="USD"),
     *                 @OA\Property(property="paid_date", type="string", format="date", example="2025-12-28"),
     *                 @OA\Property(property="note", type="string", example="دفعة أولى"),
     *                 @OA\Property(property="reason", type="string", example="دفعة نقدية")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الطالب غير موجود أو لا يوجد دفعات",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="لا يوجد دفعات للطالب")
     *         )
     *     ),
     * )
     */
    public function latestPayment($id)
    {
        $student = Student::find($id);

        if (!$student) {
            return $this->error('الطالب غير موجود', 404);
        }

        $latestPayment = $student->latestActiveEnrollmentContract
            ? $student->latestActiveEnrollmentContract->payments()->latest('paid_date')->first()
            : null;

        if (!$latestPayment) {
            return $this->error('لا يوجد دفعات للطالب', 404);
        }

        return $this->successResponse(
            new PaymentResource($latestPayment),
            'تم جلب آخر دفعة للطالب بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/students/{studentID}/exam-results/last-two-weeks",
     *     summary="جلب نتائج امتحانات الطالب للأسبوع الحالي والأسبوع الماضي (من السبت إلى الخميس)",
     *     description="
     *       يعطي جميع نتائج الامتحانات للطالب خلال آخر أسبوعين:
     *       - current_week: نتائج الأسبوع الحالي (من السبت الأخير حتى اليوم الحالي أو الخميس إذا تجاوزه)
     *       - last_week: نتائج الأسبوع الماضي (من السبت السابق للأسبوع الحالي حتى الخميس قبله)
     *     ",
     *     operationId="getStudentExamResultsLastTwoWeeks",
     *     tags={"Students"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="studentID",
     *         in="path",
     *         description="معرف الطالب",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع نتائج الامتحانات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع نتائج الامتحانات بنجاح"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_week", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="exam_id", type="integer", example=5),
     *                         @OA\Property(property="student_id", type="integer", example=1),
     *                         @OA\Property(property="obtained_marks", type="number", format="float", example=595),
     *                         @OA\Property(property="is_passed", type="boolean", example=true),
     *                         @OA\Property(property="remarks", type="string", example="ممتاز"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-29T07:38:29.000000Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-29T07:38:29.000000Z"),
     *                         @OA\Property(property="exam", type="object",
     *                             @OA\Property(property="examType", type="object"),
     *                             @OA\Property(property="batchSubject", type="object",
     *                                 @OA\Property(property="subject", type="object"),
     *                                 @OA\Property(property="batch", type="object")
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(property="last_week", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="exam_id", type="integer", example=4),
     *                         @OA\Property(property="student_id", type="integer", example=1),
     *                         @OA\Property(property="obtained_marks", type="number", format="float", example=580),
     *                         @OA\Property(property="is_passed", type="boolean", example=true),
     *                         @OA\Property(property="remarks", type="string", example="جيد جداً"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-23T10:15:00.000000Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-23T10:15:00.000000Z"),
     *                         @OA\Property(property="exam", type="object",
     *                             @OA\Property(property="examType", type="object"),
     *                             @OA\Property(property="batchSubject", type="object",
     *                                 @OA\Property(property="subject", type="object"),
     *                                 @OA\Property(property="batch", type="object")
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الطالب غير موجود أو لا توجد نتائج امتحانات",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا توجد نتائج امتحانات للطالب"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function lastTwoWeeks($studentID)
    {
        $today = Carbon::now();

        // الأسبوع الحالي: من السبت الأخير حتى اليوم الحالي (إذا اليوم بعد الخميس نعتبر الخميس نهاية الأسبوع)
        $startOfCurrentWeek = $today->copy()->previous(Carbon::SATURDAY)->startOfDay();
        $endOfCurrentWeek = $today->copy();
        if ($today->dayOfWeek > Carbon::THURSDAY) {
            // إذا اليوم الجمعة أو بعد الخميس نعتبر الخميس نهاية الأسبوع الحالي
            $endOfCurrentWeek = $startOfCurrentWeek->copy()->addDays(5)->endOfDay(); // السبت + 5 أيام = الخميس
        }

        // الأسبوع الماضي: السبت قبل الأسبوع الحالي إلى الخميس قبله
        $startOfLastWeek = $startOfCurrentWeek->copy()->subWeek()->startOfDay(); // السبت السابق للأسبوع الحالي
        $endOfLastWeek = $startOfLastWeek->copy()->addDays(5)->endOfDay(); // الخميس بعده

        // جلب النتائج حسب تاريخ إنشاء النتيجة
        $examResults = ExamResult::query()
            ->where('student_id', $studentID)
            ->whereBetween('created_at', [$startOfLastWeek, $endOfCurrentWeek])
            ->with([
                'exam.examType',
                'exam.batchSubject.subject',
                'exam.batchSubject.batch',
            ])
            ->orderByDesc('created_at')
            ->get();

        // تصنيف النتائج حسب الأسبوع
        $groupedResults = [
            'current_week' => StudentExamResultResource::collection(
                $examResults->filter(fn($er) => $er->created_at->between($startOfCurrentWeek, $endOfCurrentWeek))
            ),
            'last_week' => StudentExamResultResource::collection(
                $examResults->filter(fn($er) => $er->created_at->between($startOfLastWeek, $endOfLastWeek))
            ),
        ];

        return $this->successResponse(
            $groupedResults,
            'تم جلب جميع نتائج الامتحانات بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/students/{studentId}/exams/today-and-week",
     *     summary="جلب امتحانات الطالب لليوم والأسبوع الحالي (من السبت حتى اليوم)",
     *     description="
     *       هذا المسار يعيد امتحانات الطالب حسب التسجيل الحالي في الدفعة:
     *       - today: الامتحانات التي تجري اليوم
     *       - current_week: امتحانات الأسبوع الحالي من يوم السبت الأخير حتى اليوم الحالي
     *     ",
     *     operationId="getStudentCurrentPeriodExams",
     *     tags={"Students"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="studentId",
     *         in="path",
     *         required=true,
     *         description="معرف الطالب",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب الامتحانات لليوم والأسبوع الحالي بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب الامتحانات لليوم والأسبوع الحالي بنجاح"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="today", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="exam_id", type="integer", example=10),
     *                         @OA\Property(property="subject_name", type="string", example="رياضيات"),
     *                         @OA\Property(property="exam_date", type="string", format="date", example="2025-12-30"),
     *                         @OA\Property(property="exam_time", type="string", example="10:00"),
     *                         @OA\Property(property="exam_type", type="string", example="شفوي"),
     *                         @OA\Property(property="total_marks", type="number", example=100),
     *                         @OA\Property(property="passing_marks", type="number", example=50)
     *                     )
     *                 ),
     *                 @OA\Property(property="current_week", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="exam_id", type="integer", example=11),
     *                         @OA\Property(property="subject_name", type="string", example="فيزياء"),
     *                         @OA\Property(property="exam_date", type="string", format="date", example="2025-12-28"),
     *                         @OA\Property(property="exam_time", type="string", example="09:00"),
     *                         @OA\Property(property="exam_type", type="string", example="تحريري"),
     *                         @OA\Property(property="total_marks", type="number", example=100),
     *                         @OA\Property(property="passing_marks", type="number", example=50)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الطالب غير موجود أو لا يوجد تسجيل حالي في أي دفعة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد تسجيل حالي للطالب في أي دفعة"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function currentPeriodExams(Request $request, $studentId)
    {
        $student = Student::with('latestBatchStudent.batch.classRoom')->findOrFail($studentId);
        $today = now()->startOfDay();
        $weekStart = $today->copy()->previous(Carbon::SATURDAY)->startOfDay();
        $weekEnd = $today->copy()->endOfDay();

        $latestBatchStudent = $student->latestBatchStudent;

        if (!$latestBatchStudent || !$latestBatchStudent->batch) {
            return $this->successResponse(
                ['today' => [], 'current_week' => []],
                __('لا يوجد تسجيل حالي للطالب في أي دفعة'),
                200
            );
        }

        // جلب الـ Batch Subjects الفعالة
        $effectiveBatchSubjectIds = BatchSubject::query()
            ->where('batch_id', $latestBatchStudent->batch_id)
            ->where('is_active', true)
            ->when($latestBatchStudent->is_partial, function ($q) use ($latestBatchStudent) {
                $q->whereHas(
                    'partialBatchStudents',
                    fn($sub) =>
                    $sub->where('batch_student_id', $latestBatchStudent->id)
                        ->wherePivot('status', 'active')
                );
            })
            ->pluck('id');

        if ($effectiveBatchSubjectIds->isEmpty()) {
            return $this->successResponse(
                ['today' => [], 'current_week' => []],
                __('لا توجد مواد دراسية نشطة للطالب'),
                200
            );
        }

        // تحميل العلاقات المطلوبة (بما فيها الكلاس روم من الـ باتش)
        $withRelations = [
            'batchSubject.subject',
            'batchSubject.batch.classRoom', // الوصول للكلاس روم من الـ باتش
            'examType'
        ];

        // امتحانات اليوم
        $todayExams = Exam::query()
            ->whereIn('batch_subject_id', $effectiveBatchSubjectIds)
            ->whereDate('exam_date', $today)
            ->with($withRelations)
            ->orderBy('exam_time')
            ->get();

        // امتحانات الأسبوع الحالي
        $weekExams = Exam::query()
            ->whereIn('batch_subject_id', $effectiveBatchSubjectIds)
            ->whereBetween('exam_date', [$weekStart, $weekEnd])
            ->with($withRelations)
            ->orderBy('exam_date', 'desc')
            ->orderBy('exam_time')
            ->get();

        // تحويل البيانات باستخدام الـ Resource
        $data = [
            'today' => StudentExamResource::collection($todayExams)->resolve(),
            'current_week' => StudentExamResource::collection($weekExams)->resolve(),
            'period_info' => [
                'week_start' => $weekStart->format('Y-m-d'),
                'week_end' => $weekEnd->format('Y-m-d'),
                'today' => $today->format('Y-m-d')
            ]
        ];

        return $this->successResponse(
            $data,
            __('تم جلب الامتحانات لليوم والأسبوع الحالي بنجاح'),
            200
        );
    }
    /**
     * @OA\Get(
     *     path="/api/students/schedules",
     *     summary="جلب جدول الدوام (لطالب، شعبة، أو موقع جغرافي كامل) مع فلاتر اختيارية",
     *     description="
يعيد هذا المسار جدول الدوام حسب **المصدر المطلوب** (طالب، شعبة، أو موقع جغرافي كامل)
مع إمكانية تحديد اليوم والفلاتر الإضافية.

🧠 **آلية العمل:**
- عند `type=student`:
  - يتم تحديد شعبة الطالب الفعّالة (latestBatchStudent).
- عند `type=batch`:
  - يتم استخدام الشعبة مباشرة.
- عند `type=location`:
  - يتم جلب جميع جداول الدوام المرتبطة بجميع الشعب في الفرع الجغرافي المحدد.
- في حال عدم وجود نتائج، يتم إرجاع periods فارغة بدون خطأ.
- يتم تحديد نوع كل حصة (درس/امتحان) تلقائياً بناءً على وجود امتحانات في نفس الوقت والمادة والغرفة.

📌 **ملاحظات مهمة:**
- عدم وجود جدول **لا يعتبر خطأ** (Status Code = 200 دائمًا عند النجاح).
- عند استخدام `type=location`:
  - يجب تحديد `institute_branch_id` (إلزامي)
  - يتم تجاهل قيمة `id` (غير مطلوب)
- عند استخدام `type=student` أو `type=batch`:
  - يجب تحديد `id` (إلزامي)
  - `institute_branch_id` اختياري ويستخدم كفلتر إضافي
- **نوع الحصة (type)**:
  - `درس`: حصة عادية
  - `امتحان`: يوجد امتحان في نفس الوقت والمادة والغرفة
",
     *     tags={"Schedules"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=true,
     *         description="مصدر الجدول",
     *         @OA\Schema(
     *             type="string",
     *             enum={"student","batch","location"},
     *             example="location"
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=false,
     *         description="
المعرف (تُستخدم حسب نوع المصدر):
- عند `type=student`: student_id
- عند `type=batch`: batch_id
- عند `type=location`: غير مطلوب (يتم تجاهله)
",
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *
     *     @OA\Parameter(
     *         name="day",
     *         in="query",
     *         required=false,
     *         description="
تحديد اليوم:
- today → اليوم الحالي (افتراضي)
- all → جميع الأيام
- Sunday, Monday, ... → يوم محدد
",
     *         @OA\Schema(type="string", example="today")
     *     ),
     *
     *     @OA\Parameter(
     *         name="is_default",
     *         in="query",
     *         required=false,
     *         description="
فلترة البرنامج الافتراضي:
- true → الافتراضي فقط
- false → غير الافتراضي فقط
- غير مرسل → بدون فلترة
",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *
     *     @OA\Parameter(
     *         name="institute_branch_id",
     *         in="query",
     *         required=false,
     *         description="
فلترة حسب فرع المعهد:
- عند `type=location`: **إلزامي**
- في الأنواع الأخرى: اختياري (يُستخدم كفلتر إضافي)
- يتم التحقق عبر العلاقة:
  class_schedules → batch_subjects → batches → institute_branch_id
",
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم تنفيذ الطلب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جدول الدوام بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="periods_count",
     *                     type="integer",
     *                     example=5,
     *                     description="عدد الحصص الفعلية بدون تكرار رقم الحصة"
     *                 ),
     *                 @OA\Property(
     *                     property="periods",
     *                     type="object",
     *                     description="الحصص مجمعة حسب اليوم ثم رقم الحصة",
     *                     additionalProperties={
     *                         @OA\Schema(
     *                             type="object",
     *                             additionalProperties={
     *                                 @OA\Schema(
     *                                     type="array",
     *                                     @OA\Items(
     *                                         @OA\Property(property="batch_name", type="string", example="بكالوريا علمي شباب شتاء 2024"),
     *                                         @OA\Property(property="subject", type="string", example="اللغة العربية"),
     *                                         @OA\Property(property="class_room", type="string", example="القاعة 7"),
     *                                         @OA\Property(property="start_time", type="string", format="time", example="08:00:00"),
     *                                         @OA\Property(property="end_time", type="string", format="time", example="09:00:00"),
     *                                         @OA\Property(property="is_default", type="boolean", example=true),
     *                                         @OA\Property(
     *                                             property="type",
     *                                             type="string",
     *                                             enum={"درس","امتحان"},
     *                                             example="درس",
     *                                             description="نوع الحصة: درس عادي أو امتحان"
     *                                         ),
     *                                         @OA\Property(
     *                                             property="supervisor",
     *                                             type="object",
     *                                             nullable=true,
     *                                             @OA\Property(property="name", type="string", example="ريم صالح"),
     *                                             @OA\Property(property="photo", type="string", format="uri", nullable=true, example="https://example.com/photo.jpg")
     *                                         )
     *                                     )
     *                                 )
     *                             }
     *                         )
     *                     }
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="طلب غير صالح (مثال: نسيان institute_branch_id عند type=location)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Institute branch ID is required for location type")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="الطالب غير موجود أو غير مرتبط بدفعة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الطالب غير مرتبط بأي دفعة حالياً")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="بيانات غير صالحة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The institute_branch_id field is required when type is location."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="institute_branch_id",
     *                     type="array",
     *                     @OA\Items(type="string", example="The institute_branch_id field is required when type is location.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */



    public function getSchedule(
        GetScheduleRequest $request,
        ScheduleService $service
    ) {
        $filter = ScheduleFilter::fromRequest($request);

        // الإصلاح: معالجة الـ array الجديد الذي يعيده الـ service
        $result = $service->getSchedule($filter->toDto());
        $schedules = $result['schedules'];
        $exams = $result['exams'];

        return $this->successResponse(
            new SchedulePeriodsResource([
                'schedules' => $schedules
            ], $exams), // تمرير الامتحانات كمعامل ثاني
            $schedules->isEmpty()
                ? 'لا يوجد دوام مطابق للطلب'
                : 'تم جلب جدول الدوام بنجاح'
        );
    }




    /**
     * @OA\Get(
     *     path="/api/students/{student_id}/monthly-evaluation",
     *     summary="جلب التقييمات الشهرية للطالب (متوسط النسب المئوية لنتائج الامتحانات)",
     *     description="يُرجع تقييم شهري للطالب لمدة 12 شهراً، بدءاً من أقدم شعبة.",
     *     tags={"Students"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="student_id",
     *         in="path",
     *         required=true,
     *         description="المعرف الفريد للطالب",
     *         @OA\Schema(type="integer", example=1456)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب التقييمات الشهرية بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب التقييمات الشهرية بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="student", type="object",
     *                     @OA\Property(property="id", type="integer", example=1456),
     *                     @OA\Property(property="name", type="string", example="محمد أحمد الصغير"),
     *                     @OA\Property(property="start_from", type="string", format="year-month", example="2025-09")
     *                 ),
     *                 @OA\Property(property="evaluations_count", type="integer", example=12),
     *                 @OA\Property(
     *                     property="evaluations",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="month", type="string", example="أيلول 2025"),
     *                         @OA\Property(property="average_percentage", type="number", format="float", nullable=true, example=78.5)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="الطالب غير موجود أو غير مسجل في أي شعبة",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الطالب غير موجود")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="خطأ داخلي في الخادم",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="حدث خطأ غير متوقع أثناء جلب التقييمات الشهرية")
     *         )
     *     )
     * )
     */
    public function getMonthlyEvaluations(Request $request, $student_id)
    {
        $student = Student::find($student_id);

        if (!$student) {
            return $this->error('الطالب غير موجود', 404);
        }

        $batchStudents = $student->batchStudents()->with('batch')->get();

        if ($batchStudents->isEmpty()) {
            return $this->error('الطالب غير مسجل في أي شعبة (batch)', 404);
        }

        // أقدم تاريخ بداية لأي batch الطالب مسجل فيه
        $oldestStartDate = $batchStudents
            ->map(fn($bs) => Carbon::parse($bs->batch->start_date))
            ->min();

        $startMonth = $oldestStartDate->startOfMonth();

        $arabicMonths = [
            1  => 'كانون الثاني',
            2  => 'شباط',
            3  => 'آذار',
            4  => 'نيسان',
            5  => 'أيار',
            6  => 'حزيران',
            7  => 'تموز',
            8  => 'آب',
            9  => 'أيلول',
            10 => 'تشرين الأول',
            11 => 'تشرين الثاني',
            12 => 'كانون الأول',
        ];

        $evaluations = [];

        for ($i = 0; $i < 12; $i++) {
            $current = $startMonth->clone()->addMonths($i);

            $monthStart = $current->copy()->startOfMonth();
            $monthEnd   = $current->copy()->endOfMonth();

            $examResults = ExamResult::where('student_id', $student->id)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->with(['exam:id,total_marks,name,exam_date'])
                ->get(['id', 'exam_id', 'obtained_marks', 'created_at']);

            $averagePercentage = null;

            if ($examResults->isNotEmpty()) {
                $percentages = $examResults->map(function ($result) {
                    $total = $result->exam?->total_marks ?? 100;
                    return ($result->obtained_marks / $total) * 100;
                });

                $averagePercentage = round($percentages->avg(), 2);
            }

            $monthName = $arabicMonths[$current->month] . ' ' . $current->year;

            $evaluations[$monthName] = $averagePercentage;
        }

        $data = [
            'student_id'     => $student->id,
            'student_name'   => $student->full_name ?? trim("{$student->first_name} {$student->last_name}"),
            'start_from'     => $startMonth->format('Y-m'),
            'evaluations'    => $evaluations,
        ];

        return $this->successResponse(
            $data,
            'تم جلب التقييمات الشهرية بنجاح',
            200
        );
    }
}
