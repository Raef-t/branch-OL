<?php

namespace Modules\AcademicBranches\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\AcademicBranches\Models\AcademicBranch;
use Modules\AcademicBranches\Http\Requests\StoreAcademicBranchesRequest;
use Modules\AcademicBranches\Http\Requests\UpdateAcademicBranchesRequest;
use Modules\AcademicBranches\Http\Resources\AcademicBranchesResource;
use Modules\AcademicBranches\Http\Resources\AcademicBranchStatsResource;
use Modules\Attendances\Models\Attendance;
use Modules\Shared\Traits\SuccessResponseTrait;

class AcademicBranchesController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/academic-branches",
     *     summary="قائمة جميع الفروع الأكاديمية",
     *     tags={"AcademicBranches"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع الفروع الأكاديمية بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع الفروع الأكاديمية بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Computer Science"),
     *                     @OA\Property(property="description", type="string", example="Branch for computer studies"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد فروع أكاديمية",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي فرع أكاديمي مسجل حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $branches = AcademicBranch::orderBy('id', 'desc')->get();

        if ($branches->isEmpty()) {
            return $this->error('لا يوجد أي فرع أكاديمي مسجل حالياً', 404);
        }

        return $this->successResponse(
            AcademicBranchesResource::collection($branches),
            'تم جلب جميع الفروع الأكاديمية بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/academic-branches",
     *     summary="إضافة فرع أكاديمي جديد",
     *     tags={"AcademicBranches"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="New Branch"),
     *             @OA\Property(property="description", type="string", example="Description of new branch")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء الفرع الأكاديمي بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء الفرع الأكاديمي بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="New Branch"),
     *                 @OA\Property(property="description", type="string", example="Description of new branch"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreAcademicBranchesRequest $request)
    {
        $branch = AcademicBranch::create($request->validated());

        return $this->successResponse(
            new AcademicBranchesResource($branch),
            'تم إنشاء الفرع الأكاديمي بنجاح',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/academic-branches/{id}",
     *     summary="عرض تفاصيل فرع أكاديمي محدد",
     *     tags={"AcademicBranches"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الفرع الأكاديمي",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات الفرع الأكاديمي بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات الفرع الأكاديمي بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Computer Science"),
     *                 @OA\Property(property="description", type="string", example="Branch for computer studies"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الفرع الأكاديمي غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الفرع الأكاديمي غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $branch = AcademicBranch::find($id);

        if (!$branch) {
            return $this->error('الفرع الأكاديمي غير موجود', 404);
        }

        return $this->successResponse(
            new AcademicBranchesResource($branch),
            'تم جلب بيانات الفرع الأكاديمي بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/academic-branches/{id}",
     *     summary="تحديث بيانات فرع أكاديمي",
     *     tags={"AcademicBranches"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الفرع الأكاديمي",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Updated Branch"),
     *             @OA\Property(property="description", type="string", example="Updated description")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات الفرع الأكاديمي بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات الفرع الأكاديمي بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Updated Branch"),
     *                 @OA\Property(property="description", type="string", example="Updated description"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-02T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الفرع الأكاديمي غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الفرع الأكاديمي غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function update(UpdateAcademicBranchesRequest $request, $id)
    {
        $branch = AcademicBranch::find($id);

        if (!$branch) {
            return $this->error('الفرع الأكاديمي غير موجود', 404);
        }

        $branch->update($request->validated());

        return $this->successResponse(
            new AcademicBranchesResource($branch),
            'تم تحديث بيانات الفرع الأكاديمي بنجاح',
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/academic-branches/{id}",
     *     summary="حذف فرع أكاديمي",
     *     tags={"AcademicBranches"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الفرع الأكاديمي",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف الفرع الأكاديمي بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف الفرع الأكاديمي بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الفرع الأكاديمي غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الفرع الأكاديمي غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $branch = AcademicBranch::find($id);

        if (!$branch) {
            return $this->error('الفرع الأكاديمي غير موجود', 404);
        }

        $branch->delete();

        return $this->successResponse(
            null,
            'تم حذف الفرع الأكاديمي بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/academic-branches/{id}/subjects",
     *     summary="جلب جميع المواد التابعة لفرع أكاديمي محدد",
     *     tags={"AcademicBranches"},
     *     security={{"sanctum":{}}}, 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الفرع الأكاديمي",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب المواد بنجاح"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الفرع غير موجود أو لا يوجد مواد"
     *     )
     * )
     */
    public function getSubjects($id)
    {
        $branch = AcademicBranch::with('subjects')->find($id);

        if (!$branch) {
            return $this->error('الفرع الأكاديمي غير موجود', 404);
        }

        if ($branch->subjects->isEmpty()) {
            return $this->error('لا توجد مواد لهذا الفرع الأكاديمي', 404);
        }

        return $this->successResponse(
            $branch->subjects,
            'تم جلب جميع المواد التابعة للفرع الأكاديمي بنجاح',
            200
        );
    }   

    /**
     * @OA\Get(
     *     path="/api/academic-branches/{genderType}",
     *     summary="جلب إحصائيات الفروع الأكاديمية مع معلومات الطلاب والباتشات ونسبة الحضور اليوم",
     *     description="
     *       هذا المسار يعيد **إحصائيات لكل فرع أكاديمي**، ويشمل:
     *       - عدد الطلاب الكلي
     *       - عدد الذكور
     *       - عدد الإناث
     *       - عدد الباتشات
     *       - تفاصيل كل Batch (اسم القاعة، هل القاعة مكتملة، اسم الدورة، تاريخ البداية، المشرف، عدد الطلاب، عدد الحاضرين اليوم، نسبة الحضور اليوم)
     *
     *       يمكن فلترة البيانات حسب نوع الجنس:
     *       - all: جميع الطلاب
     *       - male: فقط الطلاب الذكور
     *       - female: فقط الطالبات الإناث
     *
     *       يمكن أيضًا فلترة الباتشات حسب الفرع الأكاديمي (institute branch) عبر تمرير query parameter:
     *       - institute_branch_id: إرجاع الباتشات فقط للفرع المحدد. إذا لم يُمرر، يتم إرجاع كل الفروع.
     *     ",
     *     tags={"AcademicBranches"},
     *     security={{"sanctum":{}}},
     *     
     *     @OA\Parameter(
     *         name="genderType",
     *         in="path",
     *         description="نوع الجنس المطلوب: all, male, female",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             enum={"all","male","female"},
     *             default="all"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="institute_branch_id",
     *         in="query",
     *         description="فلترة الباتشات حسب معرف الفرع الأكاديمي. إذا لم يُمرر، يتم إرجاع كل الفروع.",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب إحصائيات الفروع الأكاديمية بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب إحصائيات الفروع الأكاديمية بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="academic_branch_id", type="integer", example=1),
     *                     @OA\Property(property="academic_branch_name", type="string", example="فرع الرياضيات"),
     *                     @OA\Property(property="students_count", type="integer", example=120),
     *                     @OA\Property(property="male_students_count", type="integer", example=70),
     *                     @OA\Property(property="female_students_count", type="integer", example=50),
     *                     @OA\Property(property="batches_count", type="integer", example=3),
     *                     @OA\Property(
     *                         property="batches",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="batch_id", type="integer", example=10),
     *                             @OA\Property(property="batch_name", type="string", example="الدورة الصيفية"),
     *                             @OA\Property(property="class_room_name", type="string", example="قاعة رقم 3"),
     *                             @OA\Property(property="is_classroom_full", type="boolean", example=false),
     *                             @OA\Property(property="start_date", type="string", format="date", example="2025-09-01"),
     *                             @OA\Property(property="supervisor", type="object",
     *                                 @OA\Property(property="name", type="string", example="أحمد محمد"),
     *                                 @OA\Property(property="photo", type="string", example="https://example.com/photo.jpg")
     *                             ),
     *                             @OA\Property(property="students_count", type="integer", example=35),
     *                             @OA\Property(property="present_students", type="integer", example=30),
     *                             @OA\Property(property="attendance_percentage", type="integer", example=86)
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="نوع الجنس غير صالح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="نوع الجنس غير صالح"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function getDetailsForAcadimicBranches(Request $request, $genderType = 'all')
    {
        $genderType = strtolower($genderType);

        if (!in_array($genderType, ['male', 'female', 'all'])) {
            return $this->error('نوع الجنس غير صالح', 422);
        }

        $today = now()->toDateString();
        $instituteBranchId = $request->query('institute_branch_id');

        $academicBranches = AcademicBranch::with([
            'batches' => function ($query) use ($genderType, $instituteBranchId) {
                if ($genderType !== 'all') {
                    $query->forGender($genderType);
                }

                if ($instituteBranchId) {
                    $query->where('institute_branch_id', $instituteBranchId);
                }

                $query->with([
                    'classRoom',
                    'batchEmployees.employee.user',
                    'batchStudents.student',
                ]);
            }
        ])->get();

        // نسبة الحضور لكل batch
        $academicBranches->each(function ($branch) use ($today) {
            $branch->batches->each(function ($batch) use ($today) {
                $registeredStudentsCount = $batch->batchStudents->count();

                if ($registeredStudentsCount === 0) {
                    $batch->attendance_percentage = null;
                    $batch->present_students = 0;
                    return;
                }

                $presentCount = Attendance::where('batch_id', $batch->id)
                    ->where('attendance_date', $today)
                    ->where('status', 'present')
                    ->whereIn(
                        'student_id',
                        $batch->batchStudents->pluck('student_id')
                    )
                    ->distinct()
                    ->count('student_id');

                $batch->present_students = $presentCount;
                $batch->attendance_percentage = round(($presentCount / $registeredStudentsCount) * 100);
            });
        });

        return $this->successResponse(
            AcademicBranchStatsResource::collection($academicBranches),
            'تم جلب إحصائيات الفروع الأكاديمية بنجاح',
            200
        );
    }




}