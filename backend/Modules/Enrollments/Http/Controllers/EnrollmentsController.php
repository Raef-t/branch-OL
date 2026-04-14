<?php

namespace Modules\Enrollments\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Enrollments\Http\Requests\StoreEnrollmentRequest;
use Modules\Enrollments\Services\StudentEnrollmentService;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\Students\Http\Resources\StudentResource;
use Modules\Students\Models\Student;

class EnrollmentsController extends Controller
{
    use SuccessResponseTrait;
    /**
     * @OA\Get(
     *     path="/api/enrollments",
     *     summary="عرض قائمة الطلاب",
     *     description="هذه الواجهة تقوم بجلب جميع الطلاب مع معلومات العائلة والأولياء (الأب والأم أو الوصي).",
     *     operationId="getStudentsList",
     *     tags={"Enrollments"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب قائمة الطلاب بنجاح",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب الطلاب بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="first_name", type="string", example="خالد"),
     *                     @OA\Property(property="last_name", type="string", example="أحمد"),
     *                     @OA\Property(property="date_of_birth", type="string", format="date", example="2010-05-15"),
     *                     @OA\Property(property="gender", type="string", example="male"),
     *                     @OA\Property(property="branch_id", type="integer", example=2),
     *                     @OA\Property(property="status_id", type="integer", example=1),
     *                     @OA\Property(
     *                         property="family",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(
     *                             property="guardians",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=10),
     *                                 @OA\Property(property="first_name", type="string", example="أحمد"),
     *                                 @OA\Property(property="last_name", type="string", example="علي"),
     *                                 @OA\Property(property="relation", type="string", example="أب"),
     *                                 @OA\Property(property="phone", type="string", example="0999888777")
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح بالدخول",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="غير مصرح بالدخول")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="خطأ في الخادم",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="حدث خطأ غير متوقع في الخادم")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $students = Student::with(['family.guardians', 'school'])->latest()->get();
        return $this->successResponse(
            StudentResource::collection($students),
            'تم جلب الطلاب بنجاح',
            200
        );
    }

    public function create()
    {
        return view('enrollments::create');
    }
    /**
     * @OA\Post(
     *     path="/api/enrollments",
     *     summary="تسجيل طالب جديد مع إدارة ذكية للعائلات ودعم رفع الصور",
     *     description="تتيح هذه النقطة تسجيل طالب جديد مع دعم رفع الصور الشخصية وبطاقة الهوية. يتم إرسال البيانات كـ form-data.",
     *     tags={"Enrollments"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="بيانات التسجيل الكاملة للطالب وأولياء الأمور (يجب استخدام multipart/form-data)",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="student[first_name]", type="string", example="خالد", description="الاسم الأول للطالب (مطلوب)"),
     *                 @OA\Property(property="student[last_name]", type="string", example="أحمد", description="الكنية للطالب (مطلوب)"),
     *                 @OA\Property(property="student[date_of_birth]", type="string", format="date", example="2016-03-15", description="تاريخ الميلاد"),
     *                 @OA\Property(property="student[birth_place]", type="string", example="دمشق", description="مكان الولادة"),
     *                 @OA\Property(property="student[gender]", type="string", enum={"male","female"}, example="male", description="الجنس"),
     *                 @OA\Property(property="student[national_id]", type="string", example="123456789", description="الرقم الوطني"),
     *                 @OA\Property(property="student[previous_school_name]", type="string", example="مدرسة الفارابي", description="المدرسة السابقة"),
     *                 @OA\Property(property="student[how_know_institute]", type="string", example="توصية", description="كيف عرف بالمعهد؟"),
     *                 @OA\Property(property="student[notes]", type="string", example="يحتاج دعم إضافي في الرياضيات", description="ملاحظات"),
     *                 @OA\Property(property="student[qr_code_data]", type="string", description="بيانات رمز الاستجابة السريعة (إن وجدت)"),
     *                 @OA\Property(property="student[institute_branch_id]", type="integer", example=1, description="معرف فرع المعهد (مطلوب)"),
     *                 @OA\Property(property="student[branch_id]", type="integer", example=2, description="معرف الفرع الدراسي (مطلوب)"),
     *                 @OA\Property(property="student[enrollment_date]", type="string", format="date", example="2025-04-05", nullable=true, description="تاريخ التسجيل"),
     *                 @OA\Property(property="student[start_attendance_date]", type="string", format="date", example="2025-04-10", description="تاريخ بدء الحضور"),
     *                 @OA\Property(property="student[bus_id]", type="integer", example=5, nullable=true, description="معرف الحافلة (اختياري)"),
     *                 @OA\Property(property="student[school_id]", type="integer", example=1, nullable=true, description="معرف المدرسة (اختياري)"),
     *                 @OA\Property(property="student[city_id]", type="integer", example=3, nullable=true, description="معرف المدينة (اختياري)"),
     *                 @OA\Property(property="student[status_id]", type="integer", example=1, nullable=true, description="معرف حالة الطالب (اختياري)"),
     * @OA\Property(property="student[health_status]", type="string", example="سليم", description="الحالة الصحية للطالب (اختياري)"),
     * @OA\Property(property="student[psychological_status]", type="string", example="طبيعية", description="الحالة النفسية للطالب (اختياري)"),
     *
     *                 @OA\Property(property="student[profile_photo]", type="string", format="binary", description="صورة شخصية للطالب (jpeg, png, jpg, gif - حتى 2MB)"),
     *                 @OA\Property(property="student[id_card_photo]", type="string", format="binary", description="صورة بطاقة الهوية للطالب (jpeg, png, jpg, gif, pdf - حتى 2MB)"),
     *
     *                 @OA\Property(property="father[first_name]", type="string", example="أحمد", description="الاسم الأول للأب (مطلوب)"),
     *                 @OA\Property(property="father[last_name]", type="string", example="محمد", description="الكنية للأب (مطلوب)"),
     *                 @OA\Property(property="father[national_id]", type="string", example="123456789", description="(اختياري)الرقم الوطني للأب"),          
     *                 @OA\Property(property="father[occupation]", type="string", example="مهندس", description="المهنة (اختياري)"),
     *                 @OA\Property(property="father[address]", type="string", example="دمشق، المزة", description="العنوان (اختياري)"),
     *
     *                 @OA\Property(property="mother[first_name]", type="string", example="منى", description="الاسم الأول للأم (مطلوب)"),
     *                 @OA\Property(property="mother[last_name]", type="string", example="علي", description="الكنية للأم (مطلوب)"),
     *                 @OA\Property(property="mother[national_id]", type="string", example="987654321", description="(اختياري)الرقم الوطني للأم"),               
     *                 @OA\Property(property="mother[occupation]", type="string", example="معلمة", description="المهنة (اختياري)"),
     *                 @OA\Property(property="mother[address]", type="string", example="دمشق، برزة", description="العنوان (اختياري)"),
     *
     *                 @OA\Property(
     *                   property="is_existing_family_confirmed",
     *                   type="boolean",
     *                   nullable=true,
     *                   description="سلوك النظام تجاه العائلة:
     *                   - true: ربط الطالب بعائلة موجودة
     *                   - false: إنشاء عائلة جديدة فورًا
     *                   - اتركه فارغًا: التحقق من وجود عائلة أولاً"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم العثور على عائلة موجودة — انتظر تأكيد المستخدم",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم العثور على عائلة موجودة بنفس بيانات الأب والأم. هل ترغب في ربط الطالب بهذه العائلة؟"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="family", type="object",
     *                     @OA\Property(property="id", type="integer", example=123),
     *                     @OA\Property(property="guardians", type="array", @OA\Items(ref="#/components/schemas/GuardianResource")),
     *                     @OA\Property(property="students", type="array", @OA\Items(ref="#/components/schemas/StudentResource"))
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم تسجيل الطالب بنجاح (في عائلة جديدة أو موجودة)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تسجيل الطالب بنجاح"),
     *             @OA\Property(property="data", ref="#/components/schemas/StudentResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="بيانات غير صالحة (Validation Error)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="student.first_name", type="array", @OA\Items(type="string", example="الاسم الأول للطالب مطلوب."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     )
     * )
     */
    public function store(StoreEnrollmentRequest $request, StudentEnrollmentService $service)
    {
        try {
            $validated = $request->validated();
            $data = $validated;

            // معالجة رفع الصور إن وُجدت
            if ($request->hasFile('student.profile_photo')) {
                $data['student']['profile_photo'] = $request->file('student.profile_photo');
            }

            if ($request->hasFile('student.id_card_photo')) {
                $data['student']['id_card_photo'] = $request->file('student.id_card_photo');
            }

            $isConfirmed     = $validated['is_existing_family_confirmed'] ?? null;
            $confirmedFamilyId = isset($validated['confirmed_family_id']) ? (int) $validated['confirmed_family_id'] : null;

            // ─── الحالة 1: المستخدم أكد أن العائلة جديدة ━━━━━━━━━━━━━━━━━━━━━━
            if ($isConfirmed === false) {
                $student = $service->enrollStudent($validated, false);
                $student->load(['family', 'family.guardians', 'family.contactDetails.student', 'instituteBranch', 'branch', 'bus', 'status', 'city', 'school']);
                return $this->successResponse(new StudentResource($student), 'تم تسجيل الطالب كعائلة جديدة بنجاح', 201);
            }

            // ─── الحالة 2: تأكيد ربط بعائلة محددة (family_id معروف) ━━━━━━━━━━━━
            if ($isConfirmed === true && $confirmedFamilyId) {
                $student = $service->enrollStudent($validated, true, $confirmedFamilyId);
                $student->load(['family', 'family.guardians', 'family.contactDetails.student', 'instituteBranch', 'branch', 'bus', 'status', 'city', 'school']);
                return $this->successResponse(new StudentResource($student), 'تم تسجيل الطالب بنجاح', 201);
            }

            // ─── الحالة 3: لم يحدد التأكيد → تحقق من وجود عائلة ━━━━━━━━━━━━━━━━
            if ($isConfirmed === null) {
                $matchingFamilies = $service->checkExistingFamily($data['father'], $data['mother']);

                // ── عائلة واحدة متطابقة: أسك و اسأل الموظف ──────────────
                if ($matchingFamilies->count() === 1) {
                    $existingFamily = $matchingFamilies->first();
                    $matchReason = $existingFamily->matched_by ?? 'name';
                    $message = ($matchReason === 'phone') 
                        ? 'تم العثور على عائلة تملك نفس رقم الهاتف المسجل. هل ترغب في ربط الطالب بها؟'
                        : 'تم العثور على عائلة موجودة بنفس بيانات الأب والأم. هل ترغب في ربط الطالب بهذه العائلة؟';

                    return $this->successResponse([
                        'match_count' => 1,
                        'match_reason' => $matchReason,
                        'family' => [
                            'id'        => $existingFamily->id,
                            'guardians' => $existingFamily->guardians,
                            'students'  => StudentResource::collection($existingFamily->students),
                        ]
                    ], $message, 200);
                }

                // ── أكثر من عائلة متطابقة: أرجع قائمتها ليختار الموظف ──────────
                if ($matchingFamilies->count() > 1) {
                    $familiesList = $matchingFamilies->map(function ($family) {
                        return [
                            'id'           => $family->id,
                            'match_reason' => $family->matched_by ?? 'name',
                            'guardians'    => $family->guardians,
                            'students'     => StudentResource::collection($family->students),
                        ];
                    });

                    return $this->successResponse([
                        'match_count' => $matchingFamilies->count(),
                        'families'    => $familiesList,
                    ], 'تم العثور على عائلات متطابقة (بالأسماء أو الأرقام)، يرجى اختيار العائلة الصحيحة', 200);
                }
            }

            // ─── الحالة 4: تأكيد الربط بدون family_id (isConfirmed=true بدون id) أو إنشاء جديد ━━━
            $student = $service->enrollStudent($validated, $isConfirmed, $confirmedFamilyId);
            $student->load(['family', 'family.guardians', 'family.contactDetails.student', 'instituteBranch', 'branch', 'bus', 'status', 'city', 'school']);

            return $this->successResponse(new StudentResource($student), 'تم تسجيل الطالب بنجاح', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // أخطاء التحقق من البيانات (validation)
            return $this->error('خطأ في التحقق من البيانات المُرسلة: ' . $e->getMessage(), 422);
        } catch (\Illuminate\Database\QueryException $e) {
            // أخطاء قاعدة البيانات (مثل duplicate أو foreign key)
            return $this->error('حدث خطأ أثناء حفظ البيانات في قاعدة البيانات: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            // أي أخطاء أخرى غير متوقعة
            Log::error('خطأ في عملية تسجيل الطالب', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->error('حدث خطأ غير متوقع أثناء تسجيل الطالب، يرجى المحاولة لاحقًا.', 500);
        }
    }


    public function show($id)
    {
        return view('enrollments::show');
    }

    public function edit($id)
    {
        return view('enrollments::edit');
    }

    public function update(Request $request, $id) {}
    public function destroy($id) {}
}
