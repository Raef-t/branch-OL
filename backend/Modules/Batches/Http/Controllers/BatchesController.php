<?php

namespace Modules\Batches\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\Attendances\Models\Attendance;
use Modules\Batches\Models\Batch;
use Modules\BatchSubjects\Models\BatchSubject;
use Modules\Subjects\Models\Subject;
use Modules\Batches\Http\Requests\StoreBatchRequest;
use Modules\Batches\Http\Requests\UpdateBatchRequest;
use Modules\Batches\Http\Resources\BatchResource;
use Modules\BatchStudents\Models\BatchStudent;
use Modules\ExamResults\Models\ExamResult;
use Modules\Exams\Http\Resources\ExamResource;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\StudentExits\Models\StudentExitLog;

class BatchesController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/batches",
     *     summary="قائمة جميع الشعب/الدورات مع إمكانية الفلترة والتصفح",
     *     tags={"Batches"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", example=15)),
     *     @OA\Parameter(name="name", in="query", required=false, description="بحث باسم الدورة", @OA\Schema(type="string")),
     *     @OA\Parameter(name="student_name", in="query", required=false, description="بحث باسم طالب", @OA\Schema(type="string")),
     *     @OA\Parameter(name="gender", in="query", required=false, @OA\Schema(type="string", enum={"male","female","mixed"})),
     *     @OA\Parameter(name="status", in="query", required=false, description="فلتر الحالة", @OA\Schema(type="string", enum={"active","completed","archived","hidden"})),
     *     @OA\Parameter(name="academic_branch_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="institute_branch_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="include_hidden", in="query", required=false, description="تضمين الدورات المخفية", @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="include_archived", in="query", required=false, description="تضمين الدورات المؤرشفة", @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="sort_by", in="query", required=false, @OA\Schema(type="string", enum={"name","start_date","end_date","created_at"}, example="created_at")),
     *     @OA\Parameter(name="sort_dir", in="query", required=false, @OA\Schema(type="string", enum={"asc","desc"}, example="desc")),
     *
     *     @OA\Response(response=200, description="تم جلب الشعب/الدورات بنجاح"),
     *     @OA\Response(response=404, description="لا يوجد شعب/دورات")
     * )
     */
    public function index(Request $request)
    {
        $request->validate([
            'gender'              => 'nullable|in:male,female,mixed',
            'status'              => 'nullable|in:active,completed,archived,hidden',
            'name'                => 'nullable|string|max:255',
            'student_name'        => 'nullable|string|max:255',
            'academic_branch_id'  => 'nullable|integer',
            'institute_branch_id' => 'nullable|integer',
            'include_hidden'      => 'nullable|in:true,false,1,0',
            'include_archived'    => 'nullable|in:true,false,1,0',
            'per_page'            => 'nullable|integer|min:1|max:1000',
            'sort_by'             => 'nullable|in:name,start_date,end_date,created_at',
            'sort_dir'            => 'nullable|in:asc,desc',
        ]);

        $query = Batch::query();

        if ($request->boolean('include_hidden')) {
            $query->withoutGlobalScope(\Modules\Batches\Scopes\VisibleBatchScope::class);
        }

        if ($request->boolean('include_archived')) {
            $query->withoutGlobalScope(\Modules\Batches\Scopes\NonArchivedScope::class);
        }

        // ─── العلاقات الأساسية ───
        $query->with([
            'instituteBranch',
            'academicBranch',
            'classRoom',
            'batchEmployees.employee',
            'batchSubjects.subject',
        ]);

        // ─── الأعداد ───
        $query->withCount(['batchStudents', 'batchSubjects', 'batchEmployees']);

        // ─── الفلاتر ───
        $query->filterByName($request->get('name'));
        $query->filterByStudentName($request->get('student_name'));
        $query->forGender($request->get('gender'));
        $query->filterByStatus($request->get('status'));

        if ($request->filled('academic_branch_id')) {
            $query->where('academic_branch_id', $request->get('academic_branch_id'));
        }

        if ($request->filled('institute_branch_id')) {
            $query->where('institute_branch_id', $request->get('institute_branch_id'));
        }

        // المنطق الجديد يعتمد على السكوبات العالمية (Global Scopes)
        // لا حاجة لفلترة يدوية للأرشفة هنا لأن السكوب يقوم بذلك تلقائياً

        // ─── الترتيب ───
        $sortBy  = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        // ─── Pagination ───
        $perPage = $request->get('per_page', 15);
        $batches = $query->paginate($perPage);

        if ($batches->isEmpty()) {
            return $this->successResponse(
                [],
                'لا يوجد أي شعب/دورات مسجلة حالياً',
                200
            );
        }

        return $this->successResponse(
            [
                'batches' => BatchResource::collection($batches),
                'pagination' => [
                    'current_page'  => $batches->currentPage(),
                    'last_page'     => $batches->lastPage(),
                    'per_page'      => $batches->perPage(),
                    'total'         => $batches->total(),
                ],
            ],
            'تم جلب جميع الشعب/الدورات بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/batches",
     *     summary="إضافة شعبة/دورة جديدة",
     *     tags={"Batches"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={
     *                 "name"
     *             },
     *
     *             @OA\Property(
     *                 property="institute_branch_id",
     *                 type="integer",
     *                 example=1,
     *                 description="معرف فرع المعهد",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="academic_branch_id",
     *                 type="integer",
     *                 example=3,
     *                 description="معرف الفرع الأكاديمي",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="class_room_id",
     *                 type="integer",
     *                 example=5,
     *                 description="معرف القاعة التي تتبع لها الشعبة/الدورة",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="شعبة الصيف 2025"
     *             ),
     *             @OA\Property(
     *                 property="start_date",
     *                 type="string",
     *                 format="date",
     *                 example="2025-06-01",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="end_date",
     *                 type="string",
     *                 format="date",
     *                 example="2025-09-01",
     *                 nullable=true
     *             ),
     *
     *             @OA\Property(
     *                 property="gender_type",
     *                 type="string",
     *                 enum={"male", "female", "mixed"},
     *                 example="female",
     *                 description="نوع الجنس الخاص بالدورة (male / female / mixed)",
     *                 nullable=true
     *             ),
     *
     *             @OA\Property(
     *                 property="is_archived",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="is_hidden",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="is_completed",
     *                 type="boolean",
     *                 example=false
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء الشعبة/الدورة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء الشعبة/الدورة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *
     *                 @OA\Property(property="institute_branch_id", type="integer", example=1),
     *                 @OA\Property(property="academic_branch_id", type="integer", example=3),
     *                 @OA\Property(property="class_room_id", type="integer", example=5),
     *
     *                 @OA\Property(property="name", type="string", example="شعبة الصيف 2025"),
     *                 @OA\Property(property="start_date", type="string", format="date", example="2025-06-01"),
     *                 @OA\Property(property="end_date", type="string", format="date", example="2025-09-01"),
     *
     *                 @OA\Property(property="gender_type", type="string", example="female"),
     *
     *                 @OA\Property(property="is_archived", type="boolean", example=false),
     *                 @OA\Property(property="is_hidden", type="boolean", example=false),
     *                 @OA\Property(property="is_completed", type="boolean", example=false),
     *
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T12:15:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-29T12:15:00Z")
     *             )
     *         )
     *     )
     * )
     */

    public function store(StoreBatchRequest $request)
    {
        $batch = Batch::create($request->validated());

        // التحقق من وجود فرع أكاديمي لإضافة مواده تلقائياً
        if ($batch->academic_branch_id) {
            $academicBranch = $batch->academicBranch;
            if ($academicBranch) {
                $subjects = $academicBranch->subjects;
                
                foreach ($subjects as $subject) {
                    \Modules\BatchSubjects\Models\BatchSubject::create([
                        'batch_id' => $batch->id,
                        'subject_id' => $subject->id,
                        'assignment_date' => \Carbon\Carbon::now(),
                        'is_active' => true,
                        // instructor_subject_id سيكون null حالياً ليتم تعيينه لاحقاً
                    ]);
                }
            }
        }

        // تحميل العلاقات التي يعتمد عليها الـ Resource
        $batch->load([
            'classRoom',
            'academicBranch',
            'instituteBranch',
        ]);

        return $this->successResponse(
            [
                'batch' => new BatchResource($batch),
            ],
            'تم إنشاء الشعبة/الدورة بنجاح مع إضافة المواد التابعة للفرع الأكاديمي تلقائياً',
            201
        );
    }


    /**
     * @OA\Get(
     *     path="/api/batches/{id}/exams",
     *     summary="جلب جميع الامتحانات الخاصة بشعبة/دورة محددة",
     *     tags={"Batches"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الشعبة/الدورة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب الامتحانات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب الامتحانات الخاصة بالشعبة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="batch_subject_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="امتحان نصف الفصل"),
     *                     @OA\Property(property="exam_date", type="string", format="date", example="2025-07-15"),
     *                     @OA\Property(property="total_marks", type="integer", example=100),
     *                     @OA\Property(property="passing_marks", type="integer", example=60),
     *                     @OA\Property(property="status", type="string", example="scheduled"),
     *                     @OA\Property(property="exam_type", type="string", example="midterm"),
     *                     @OA\Property(property="remarks", type="string", example="ملاحظات إضافية"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T12:15:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-29T12:15:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الشعبة/الدورة غير موجودة أو لا توجد امتحانات",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الشعبة/الدورة غير موجودة أو لا توجد امتحانات"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function getExams($id)
    {
        $batch = Batch::with('batchSubjects.exams')->find($id);

        if (!$batch) {
            return $this->error('الشعبة/الدورة غير موجودة', 404);
        }

        $exams = $batch->batchSubjects
            ->flatMap(fn($batchSubject) => $batchSubject->exams);

        if ($exams->isEmpty()) {
            return $this->error('لا توجد امتحانات لهذه الشعبة', 404);
        }

        return $this->successResponse(
            ExamResource::collection($exams),
            'تم جلب الامتحانات الخاصة بالشعبة بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/batches/{id}",
     *     summary="عرض تفاصيل شعبة/دورة محددة (مع الأعداد والموظفين)",
     *     tags={"Batches"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الشعبة/الدورة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات الشعبة/الدورة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات الشعبة/الدورة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="شعبة الصيف 2025"),
     *                 @OA\Property(property="students_count", type="integer", example=30),
     *                 @OA\Property(property="subjects_count", type="integer", example=5),
     *                 @OA\Property(property="instructors_count", type="integer", example=3),
     *                 @OA\Property(property="is_archived", type="boolean", example=false)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الشعبة/الدورة غير موجودة"
     *     )
     * )
     */
    public function show($id)
    {
        $batch = Batch::with([
            // علاقات أساسية للـ Resource
            'classRoom',
            'academicBranch',
            'instituteBranch',

            // الموظفون
            'batchEmployees.employee',

            // علاقات التدريس
            'batchSubjects.subject',
            'batchSubjects.instructorSubject.instructor',
        ])
        ->withCount(['batchStudents', 'batchSubjects', 'batchEmployees'])
        ->find($id);

        if (!$batch) {
            return $this->error('الشعبة/الدورة غير موجودة', 404);
        }

        return $this->successResponse(
            new BatchResource($batch),
            'تم جلب بيانات الشعبة/الدورة بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/batches/{id}",
     *     summary="تحديث بيانات شعبة/دورة",
     *     tags={"Batches"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الشعبة/الدورة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="institute_branch_id",
     *                 type="integer",
     *                 example=2,
     *                 description="معرف فرع المعهد",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="academic_branch_id",
     *                 type="integer",
     *                 example=4,
     *                 description="معرف الفرع الأكاديمي",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="class_room_id",
     *                 type="integer",
     *                 example=6,
     *                 description="معرف القاعة المرتبطة بالشعبة/الدورة",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="شعبة الشتاء 2025"
     *             ),
     *             @OA\Property(
     *                 property="start_date",
     *                 type="string",
     *                 format="date",
     *                 example="2025-12-01",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="end_date",
     *                 type="string",
     *                 format="date",
     *                 example="2026-03-01",
     *                 nullable=true
     *             ),
     *
     *             @OA\Property(
     *                 property="gender_type",
     *                 type="string",
     *                 enum={"male", "female", "mixed"},
     *                 example="male",
     *                 description="نوع الجنس الخاص بالدورة (male / female / mixed)",
     *                 nullable=true
     *             ),
     *
     *             @OA\Property(property="is_archived", type="boolean", example=true),
     *             @OA\Property(property="is_hidden", type="boolean", example=true),
     *             @OA\Property(property="is_completed", type="boolean", example=true)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات الشعبة/الدورة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات الشعبة/الدورة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *
     *                 @OA\Property(property="institute_branch_id", type="integer", example=2),
     *                 @OA\Property(property="academic_branch_id", type="integer", example=4),
     *                 @OA\Property(property="class_room_id", type="integer", example=6),
     *
     *                 @OA\Property(property="name", type="string", example="شعبة الشتاء 2025"),
     *                 @OA\Property(property="start_date", type="string", format="date", example="2025-12-01"),
     *                 @OA\Property(property="end_date", type="string", format="date", example="2026-03-01"),
     *
     *                 @OA\Property(property="gender_type", type="string", example="male"),
     *
     *                 @OA\Property(property="is_archived", type="boolean", example=true),
     *                 @OA\Property(property="is_hidden", type="boolean", example=true),
     *                 @OA\Property(property="is_completed", type="boolean", example=true),
     *
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T12:15:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-29T12:15:30Z")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="الشعبة/الدورة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الشعبة/الدورة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */


    public function update(UpdateBatchRequest $request, $id)
    {
        // استخدام withoutGlobalScopes للسماح بتعديل الشعب المخفية والمؤرشفة
        $batch = Batch::withoutGlobalScopes()->find($id);

        if (!$batch) {
            return $this->error('الشعبة/الدورة غير موجودة', 404);
        }

        $batch->update($request->validated());

        // 👇 تحميل العلاقات التي يعتمد عليها الـ Resource
        $batch->load([
            'classRoom',
            'academicBranch',
            'instituteBranch',
        ]);

        return $this->successResponse(
            new BatchResource($batch),
            'تم تحديث بيانات الشعبة/الدورة بنجاح',
            200
        );
    }


    /**
     * @OA\Delete(
     *     path="/api/batches/{id}",
     *     summary="حذف شعبة/دورة",
     *     tags={"Batches"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الشعبة/الدورة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف الشعبة/الدورة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف الشعبة/الدورة بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الشعبة/الدورة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الشعبة/الدورة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        // استخدام withoutGlobalScopes للسماح بحذف الشعب المخفية والمؤرشفة
        $batch = Batch::withoutGlobalScopes()->find($id);

        if (!$batch) {
            return $this->error('الشعبة/الدورة غير موجودة', 404);
        }

        $batch->delete();

        return $this->successResponse(
            null,
            'تم حذف الشعبة/الدورة بنجاح',
            200
        );
    }

    /**
     * تبديل حالة الشعبة (إخفاء / أرشفة / إكمال)
     * PATCH /api/batches/{id}/toggle-status
     */
    public function toggleStatus(Request $request, $id)
    {
        $request->validate([
            'field' => 'required|in:is_hidden,is_archived,is_completed',
        ]);

        $batch = Batch::withoutGlobalScopes()->find($id);

        if (!$batch) {
            return $this->error('الشعبة/الدورة غير موجودة', 404);
        }

        $field = $request->input('field');
        $batch->$field = !$batch->$field;
        $batch->save();

        $batch->load(['classRoom', 'academicBranch', 'instituteBranch']);

        $labels = [
            'is_hidden' => $batch->$field ? 'تم إخفاء الشعبة' : 'تم إظهار الشعبة',
            'is_archived' => $batch->$field ? 'تم أرشفة الشعبة' : 'تم إلغاء أرشفة الشعبة',
            'is_completed' => $batch->$field ? 'تم تحديد الشعبة كمكتملة' : 'تم إلغاء اكتمال الشعبة',
        ];

        return $this->successResponse(
            new BatchResource($batch),
            $labels[$field],
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/batches/stats",
     *     summary="إحصائيات الشعب/الدورات (مكتملة وغير مكتملة)",
     *     tags={"Batches"},
     *     security={{"sanctum":{}}}, 
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب الإحصائيات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب الإحصائيات بنجاح"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="completed", type="integer", example=12),
     *                 @OA\Property(property="not_completed", type="integer", example=8),
     *                 @OA\Property(property="total", type="integer", example=20)
     *             )
     *         )
     *     )
     * )
     */
    public function getStats(Request $request)
    {
        // جلب جميع الدورات بما فيها المخفية والمؤرشفة لحساب الإحصائيات
        $allBatches = Batch::withoutGlobalScopes([
            \Modules\Batches\Scopes\VisibleBatchScope::class,
            \Modules\Batches\Scopes\NonArchivedScope::class
        ]);

        if ($request->filled('institute_branch_id')) {
            $allBatches->where('institute_branch_id', $request->get('institute_branch_id'));
        }

        $completed    = (clone $allBatches)->where('is_completed', true)->count();
        $notCompleted = (clone $allBatches)->where('is_completed', false)->count();
        $archived     = (clone $allBatches)->where('is_archived', true)->count();
        $hidden       = (clone $allBatches)->where('is_hidden', true)->count();
        $active       = (clone $allBatches)->where('is_archived', false)
                                            ->where('is_hidden', false)->count();
        $total        = (clone $allBatches)->count();

        // إحصائيات الطلاب والمواد (إضافية)
        // ملاحظة: قد تكون هذه الإحصائيات ثقيلة قليلاً، لذا نستخدم clone ونحسبها بدقة
        $totalStudents = (clone $allBatches)->withCount('batchStudents')->get()->sum('batch_students_count');
        $totalSubjects = (clone $allBatches)->withCount('batchSubjects')->get()->sum('batch_subjects_count');

        return $this->successResponse(
            [
                'active'         => $active,
                'completed'      => $completed,
                'not_completed'  => $notCompleted,
                'archived'       => $archived,
                'hidden'         => $hidden,
                'total'          => $total,
                'total_students' => $totalStudents,
                'total_subjects' => $totalSubjects,
            ],
            'تم جلب الإحصائيات بنجاح',
            200
        );
    }
    /**
     * @OA\Get(
     *     path="/api/batches/{batch}/students/last-attendance",
     *     operationId="getBatchStudentsLastAttendance",
     *     summary="جلب آخر سجل حضور وانصراف لكل طالب في دفعة",
     *     description="
     * يقوم هذا المسار بإرجاع **آخر سجل حضور وانصراف** لكل طالب مسجّل في الدفعة المحددة.
     *
     * 🧠 **آلية العمل:**
     * - يتم التحقق أولًا من وجود الدفعة.
     * - يتم جلب جميع الطلاب المرتبطين بالدفعة من جدول `batch_student`.
     * - لكل طالب:
     *   - يتم تحديد آخر سجل حضور من جدول `attendances`.
     *   - يتم البحث عن آخر سجل انصراف مطابق لنفس تاريخ الحضور من جدول `student_exit_logs`.
     * - يتم دمج بيانات الحضور والانصراف في سجل واحد لكل طالب.
     *
     * 📌 **سلوكيات مؤكدة:**
     * - في حال كانت الدفعة موجودة ولكن لا تحتوي على طلاب، يتم إرجاع استجابة ناجحة مع قائمة فارغة ورسالة توضيحية.
     * - في حال وجود طلاب دون أي سجلات حضور، يتم تجاهلهم وعدم إظهارهم في النتيجة.
     * - لا يتم تطبيق أي نطاق زمني (أسبوع / شهر)، ويتم دائمًا الاعتماد على آخر سجل فقط.
     * - هذا المسار للعرض فقط ولا يقوم بإنشاء أو تعديل أي بيانات.
     *
     * 🎯 **الاستخدام الشائع:**
     * - شاشة إدارة الحضور اليومية.
     * - عرض الحالة الحالية لطلاب الدفعة.
     * - التقارير السريعة للمشرفين.
     * ",
     *     tags={"Batches","Attendance"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="batch",
     *         in="path",
     *         required=true,
     *         description="معرّف الدفعة المطلوب جلب سجلات طلابها",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب البيانات بنجاح",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     title="نتيجة تحتوي على طلاب لديهم سجلات حضور",
     *                     type="object",
     *                     required={"batch_id","count","students"},
     *                     @OA\Property(
     *                         property="batch_id",
     *                         type="integer",
     *                         example=1,
     *                         description="معرّف الدفعة"
     *                     ),
     *                     @OA\Property(
     *                         property="count",
     *                         type="integer",
     *                         example=3,
     *                         description="عدد الطلاب الذين لديهم سجل حضور فعلي"
     *                     ),
     *                     @OA\Property(
     *                         property="students",
     *                         type="array",
     *                         description="قائمة آخر سجل حضور وانصراف لكل طالب",
     *                         @OA\Items(
     *                             type="object",
     *                             required={"student_id","student_name","date","status"},
     *                             @OA\Property(property="student_id", type="integer", example=5),
     *                             @OA\Property(property="student_name", type="string", example="أحمد السيد"),
     *                             @OA\Property(property="date", type="string", format="date", example="2025-03-06"),
     *                             @OA\Property(property="check_in", type="string", example="07:55", nullable=true),
     *                             @OA\Property(property="check_out", type="string", example="13:40", nullable=true),
     *                             @OA\Property(
     *                                 property="status",
     *                                 type="string",
     *                                 enum={"present","absent","late"},
     *                                 example="present"
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Schema(
     *                     title="دفعة موجودة بدون طلاب",
     *                     type="object",
     *                     required={"batch_id","count","students","message"},
     *                     @OA\Property(
     *                         property="batch_id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="count",
     *                         type="integer",
     *                         example=0
     *                     ),
     *                     @OA\Property(
     *                         property="students",
     *                         type="array",
     *                         description="قائمة الطلاب (فارغة)",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="student_id", type="integer"),
     *                             @OA\Property(property="student_name", type="string"),
     *                             @OA\Property(property="date", type="string"),
     *                             @OA\Property(property="check_in", type="string", nullable=true),
     *                             @OA\Property(property="check_out", type="string", nullable=true),
     *                             @OA\Property(property="status", type="string")
     *                         ),
     *                         example={}
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         example="لا يوجد طلاب مسجلون في هذه الدفعة"
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="الدفعة غير موجودة",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="الدفعة غير موجودة"
     *             )
     *         )
     *     )
     * )
     */

    public function batchLastAttendance($batchId)
    {
        // 1) تحقق من وجود الدفعة
        $batch = Batch::find($batchId);
        if (!$batch) {
            return response()->json([
                'message' => 'الدفعة غير موجودة',
            ], 404);
        }

        // 2) جلب طلاب الدفعة
        $batchStudents = BatchStudent::with('student')
            ->where('batch_id', $batchId)
            ->get();

        // ✅ الأفضل: 200 وقائمة فارغة (بدون اعتبارها خطأ)
        if ($batchStudents->isEmpty()) {
            return response()->json([
                'batch_id' => (int) $batchId,
                'count'    => 0,
                'students' => [],
                'message'  => 'لا يوجد طلاب مسجلون في هذه الدفعة',
            ], 200);
        }

        $studentsData = [];

        foreach ($batchStudents as $batchStudent) {
            $studentId = $batchStudent->student_id;

            $attendance = Attendance::where('student_id', $studentId)
                ->orderByDesc('attendance_date')
                ->first();

            // إذا لا يوجد حضور إطلاقاً لهذا الطالب -> تجاهله
            if (!$attendance) {
                continue;
            }

            $date = Carbon::parse($attendance->attendance_date)->toDateString();

            $exit = StudentExitLog::where('student_id', $studentId)
                ->whereDate('exit_date', $date)
                ->orderByDesc('exit_time')
                ->first();

            $studentsData[] = [
                'student_id'   => $studentId,
                'student_name' => $batchStudent->student?->full_name, // بعد إضافة accessor full_name
                'date'         => $date,
                'check_in'     => $attendance->recorded_at
                    ? Carbon::parse($attendance->recorded_at)->format('H:i')
                    : null,
                'check_out'    => $exit
                    ? Carbon::parse($exit->exit_time)->format('H:i')
                    : null,
                'status'       => $attendance->status,
            ];
        }

        return response()->json([
            'batch_id' => (int) $batchId,
            'count'    => count($studentsData),
            'students' => $studentsData,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/batches/averages",
     *     summary="جلب معدلات الدفعات",
     *     description="حساب المعدل العام لكل دفعة بناءً على نسب نتائج الطلاب في الامتحانات. يتم أخذ الدفعات الظاهرة فقط (is_hidden=0). يمكن فلترة النتائج حسب فرع المعهد أو الفرع الأكاديمي.",
     *     tags={"Batches"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="institute_branch_id",
     *         in="query",
     *         required=false,
     *         description="معرف فرع المعهد لتصفية الدفعات. إذا تم توفيره، سيتم إرجاع الدفعات التابعة لهذا الفرع فقط.",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="academic_branch_id",
     *         in="query",
     *         required=false,
     *         description="معرف الفرع الأكاديمي لتصفية الدفعات. إذا تم توفيره، سيتم إرجاع الدفعات التابعة لهذا الفرع الأكاديمي فقط.",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب معدلات الدفعات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب معدلات الدفعات بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="batch_name", type="string", example="بكالوريا أدبي شتاء 2024"),
     *                     @OA\Property(property="overall_average_marks", type="number", format="float", example=78.45)
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="لم يتم العثور على دفعات",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لم يتم العثور على دفعات"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح"
     *     )
     * )
     */
    public function getBatchesAverages()
    {
        $instituteBranchId = request()->query('institute_branch_id');
        $academicBranchId  = request()->query('academic_branch_id');

        $batches = Batch::with([
            'batchStudents.student.examResults.exam:id,total_marks'
        ])
            ->when(
                $instituteBranchId,
                fn($q) => $q->where('institute_branch_id', $instituteBranchId)
            )
            ->when(
                $academicBranchId,
                fn($q) => $q->where('academic_branch_id', $academicBranchId)
            )
            ->get(['id', 'name']);

        if ($batches->isEmpty()) {
            return $this->successResponse([], 'No batches found', 200);
        }

        $result = [];

        foreach ($batches as $batch) {
            $students = $batch->batchStudents
                ->pluck('student')
                ->filter()
                ->unique('id')
                ->values();

            if ($students->isEmpty()) {
                $result[] = [
                    'batch_name' => $batch->name,
                    'overall_average_marks' => 0
                ];
                continue;
            }

            foreach ($students as $student) {
                $examResults = $student->examResults->filter(
                    fn($result) => $result->exam && $result->exam->total_marks > 0
                );

                $student->calculated_average = $examResults->isEmpty()
                    ? 0
                    : $examResults->sum(
                        fn($result) => ($result->obtained_marks / $result->exam->total_marks) * 100
                    ) / $examResults->count();
            }

            $overallAverage = round($students->avg('calculated_average'), 2);

            $result[] = [
                'batch_name' => $batch->name,
                'overall_average_marks' => $overallAverage
            ];
        }

        return $this->successResponse($result, 'تم جلب معدلات الدفعات بنجاح', 200);
    }

    /**
     * @OA\Get(
     *     path="/api/batches/exam-results/exam/last-two-weeks",
     *     operationId="getExamResultsLastTwoWeeks",
     *     summary="جلب علامات امتحان معين لطلاب دفعة خلال الأسبوع الحالي والماضي",
     *     description="
     * يقوم هذا المسار بإرجاع **علامات جميع الطلاب** في دفعة محددة لامتحان محدد،
     * مع تقسيم النتائج إلى **الأسبوع الحالي** و **الأسبوع الماضي**.
     *
     * 🧠 **آلية العمل:**
     * - يتم استقبال `batch_id` و `exam_id` كـ query parameters.
     * - يتم تحديد نطاق زمني مكوّن من:
     *   - الأسبوع الحالي (من السبت إلى الخميس أو اليوم الحالي).
     *   - الأسبوع الماضي (السبت إلى الخميس السابق).
     * - يتم جلب جميع نتائج الامتحانات المرتبطة:
     *   - بالدفعة المحددة.
     *   - بالامتحان المحدد.
     * - يتم تجميع النتائج حسب الطالب.
     * - يتم حساب النسبة المئوية لكل امتحان عند توفر العلامة الكاملة.
     *
     * 📌 **سلوكيات مؤكدة:**
     * - في حال عدم إرسال `batch_id` أو `exam_id` يتم إرجاع خطأ تحقق (422).
     * - يتم تجاهل أي نتائج خارج النطاق الزمني المحدد.
     * - إذا لم يوجد نتائج في أحد الأسبوعين، يتم إرجاع مصفوفة فارغة لذلك الأسبوع.
     * - لا يتم إنشاء أو تعديل أي بيانات (عرض فقط).
     *
     * 🎯 **الاستخدام الشائع:**
     * - شاشة متابعة أداء الطلاب لامتحان محدد.
     * - مقارنة أداء الطلاب بين أسبوعين متتاليين.
     * - تقارير إشرافية سريعة للمدرس أو الإدارة.
     * ",
     *     tags={"Batches"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="batch_id",
     *         in="query",
     *         required=true,
     *         description="معرّف الدفعة المطلوب جلب علامات طلابها",
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *
     *     @OA\Parameter(
     *         name="exam_id",
     *         in="query",
     *         required=true,
     *         description="معرّف الامتحان المطلوب جلب علاماته",
     *         @OA\Schema(type="integer", example=21)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب علامات الامتحان للأسبوعين بنجاح",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"current_week","last_week"},
     *             @OA\Property(
     *                 property="current_week",
     *                 type="array",
     *                 description="نتائج الأسبوع الحالي مجمّعة حسب الطالب",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"student_id","student_name","results"},
     *                     @OA\Property(property="student_id", type="integer", example=12),
     *                     @OA\Property(property="student_name", type="string", example="محمد أحمد"),
     *                     @OA\Property(
     *                         property="results",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             required={"exam_id","exam_name","obtained_marks","total_marks","percentage","date"},
     *                             @OA\Property(property="exam_id", type="integer", example=21),
     *                             @OA\Property(property="exam_name", type="string", example="اختبار رياضيات قصير"),
     *                             @OA\Property(property="obtained_marks", type="number", example=18),
     *                             @OA\Property(property="total_marks", type="number", example=20),
     *                             @OA\Property(property="percentage", type="number", format="float", example=90),
     *                             @OA\Property(property="date", type="string", format="date", example="2025-03-06")
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="last_week",
     *                 type="array",
     *                 description="نتائج الأسبوع الماضي مجمّعة حسب الطالب",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"student_id","student_name","results"},
     *                     @OA\Property(property="student_id", type="integer", example=12),
     *                     @OA\Property(property="student_name", type="string", example="محمد أحمد"),
     *                     @OA\Property(
     *                         property="results",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="exam_id", type="integer", example=21),
     *                             @OA\Property(property="exam_name", type="string", example="اختبار رياضيات قصير"),
     *                             @OA\Property(property="obtained_marks", type="number", example=15),
     *                             @OA\Property(property="total_marks", type="number", example=20),
     *                             @OA\Property(property="percentage", type="number", format="float", example=75),
     *                             @OA\Property(property="date", type="string", format="date", example="2025-02-27")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="بيانات غير مكتملة",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="batch_id and exam_id are required")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح"
     *     )
     * )
     */
    public function examResultsLastTwoWeeks(Request $request)
    {
        $batchId  = $request->query('batch_id');
        $examId   = $request->query('exam_id');

        if (!$batchId || !$examId) {
            return $this->error('batch_id and exam_id are required', 422);
        }

        $today = Carbon::now();

        // ===== الأسبوع الحالي =====
        $startOfCurrentWeek = $today->copy()->previous(Carbon::SATURDAY)->startOfDay();
        $endOfCurrentWeek   = $today->copy();
        if ($today->dayOfWeek > Carbon::THURSDAY) {
            $endOfCurrentWeek = $startOfCurrentWeek->copy()->addDays(5)->endOfDay();
        }

        // ===== الأسبوع الماضي =====
        $startOfLastWeek = $startOfCurrentWeek->copy()->subWeek()->startOfDay();
        $endOfLastWeek   = $startOfLastWeek->copy()->addDays(5)->endOfDay();

        // ===== جلب نتائج الامتحانات =====
        $examResults = ExamResult::query()
            ->whereBetween('created_at', [$startOfLastWeek, $endOfCurrentWeek])
            ->where('exam_id', $examId)
            ->whereHas('exam.batchSubject', function ($q) use ($batchId) {
                $q->where('batch_id', $batchId);
            })
            ->with([
                'student:id,first_name,last_name',
                'exam:id,name,total_marks,batch_subject_id',
            ])
            ->orderBy('created_at')
            ->get();

        // ===== تجميع النتائج حسب الطالب =====
        $groupByStudent = function ($collection) {
            return $collection
                ->groupBy('student_id')
                ->map(function ($results) {
                    $student = $results->first()->student;
                    return [
                        'student_id'   => $student->id,
                        'student_name' => $student->full_name,
                        'results'      => $results->map(function ($r) {
                            return [
                                'exam_id'        => $r->exam_id,
                                'exam_name'      => $r->exam->name,
                                'obtained_marks' => $r->obtained_marks,
                                'total_marks'    => $r->exam->total_marks,
                                'percentage'     => $r->exam->total_marks > 0
                                    ? round(($r->obtained_marks / $r->exam->total_marks) * 100, 2)
                                    : null,
                                'date'           => $r->created_at->toDateString(),
                            ];
                        })->values(),
                    ];
                })
                ->values();
        };

        return $this->successResponse([
            'current_week' => $groupByStudent(
                $examResults->filter(fn($r) => $r->created_at->between($startOfCurrentWeek, $endOfCurrentWeek))
            ),
            'last_week' => $groupByStudent(
                $examResults->filter(fn($r) => $r->created_at->between($startOfLastWeek, $endOfLastWeek))
            ),
        ], 'تم جلب علامات الامتحان للأسبوعين بنجاح', 200);
    }

    /**
     * @OA\Get(
     *     path="/api/batches/{batch_id}/exams/last-two-weeks",
     *     operationId="getBatchExamsLastTwoWeeks",
     *     summary="جلب جميع الامتحانات لشعبة خلال الأسبوع الحالي والماضي",
     *     description="
     * يقوم هذا المسار بإرجاع **جميع الامتحانات المرتبطة بشعبة معينة**،
     * مع تقسيمها إلى **الأسبوع الحالي** و **الأسبوع الماضي**.
     * ",
     *     tags={"Batches"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="batch_id",
     *         in="path",
     *         required=true,
     *         description="معرّف الشعبة المطلوب جلب امتحاناتها",
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب الامتحانات بنجاح"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الشعبة غير موجودة",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Batch not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح"
     *     )
     * )
     */
    public function examsLastTwoWeeks($batchId)
    {
        $batch = \Modules\Batches\Models\Batch::find($batchId);

        if (!$batch) {
            return $this->error('Batch not found', 404);
        }

        $today = \Carbon\Carbon::now();

        // ===== الأسبوع الحالي (السبت - الخميس) =====
        $startOfCurrentWeek = $today->copy()->previous(\Carbon\Carbon::SATURDAY)->startOfDay();
        $endOfCurrentWeek   = $today->copy();
        if ($today->dayOfWeek > \Carbon\Carbon::THURSDAY) {
            $endOfCurrentWeek = $startOfCurrentWeek->copy()->addDays(5)->endOfDay();
        }

        // ===== الأسبوع الماضي =====
        $startOfLastWeek = $startOfCurrentWeek->copy()->subWeek()->startOfDay();
        $endOfLastWeek   = $startOfLastWeek->copy()->addDays(5)->endOfDay();

        // ===== جلب الامتحانات =====
        $exams = \Modules\Exams\Models\Exam::query()
            ->whereHas('batchSubject', fn($q) => $q->where('batch_id', $batchId))
            ->orderBy('exam_date')
            ->get();

        $groupByWeek = function ($collection, $start, $end) {
            return $collection
                ->filter(fn($exam) => $exam->exam_date->between($start, $end))
                ->map(fn($exam) => [
                    'exam_id'    => $exam->id,
                    'exam_name'  => $exam->name,
                    'exam_date'  => $exam->exam_date->toDateString(),
                    'total_marks'=> $exam->total_marks,
                ])
                ->values();
        };

        return $this->successResponse([
            'current_week' => $groupByWeek($exams, $startOfCurrentWeek, $endOfCurrentWeek),
            'last_week'    => $groupByWeek($exams, $startOfLastWeek, $endOfLastWeek),
        ], 'تم جلب الامتحانات للأسبوعين بنجاح', 200);
    }

}
