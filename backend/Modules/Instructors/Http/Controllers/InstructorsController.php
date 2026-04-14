<?php

namespace Modules\Instructors\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Batches\Models\Batch;
use Modules\Instructors\Models\Instructor;
use Modules\Instructors\Http\Requests\StoreInstructorRequest;
use Modules\Instructors\Http\Requests\UpdateInstructorPhotoRequest;
use Modules\Instructors\Http\Requests\UpdateInstructorRequest;
use Modules\Instructors\Http\Resources\InstructorResource;
use Modules\InstructorSubjects\Models\InstructorSubject;
use Modules\Shared\Traits\SuccessResponseTrait;

class InstructorsController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/teachers",
     *     summary="قائمة جميع المدرسين",
     *     tags={"Teachers"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع المدرسين بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع المدرسين بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="أحمد محمد"),
     *                     @OA\Property(property="institute_branch_id", type="integer", example=1),
     *                     @OA\Property(property="phone", type="string", example="+963123456789"),
     *                     @OA\Property(property="specialization", type="string", example="برمجة"),
     *                     @OA\Property(property="hire_date", type="string", format="date", example="2023-01-01"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد مدرسين",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي مدرس مسجل حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $instructors = Instructor::with('instituteBranch:id,name')
            ->orderByDesc('id') // عكس الترتيب
            ->get();

        if ($instructors->isEmpty()) {
            return $this->error('لا يوجد أي مدرس مسجل حالياً', 404);
        }

        return $this->successResponse(
            InstructorResource::collection($instructors),
            'تم جلب جميع المدرسين بنجاح',
            200
        );
    }
/**
 * @OA\Post(
 *     path="/api/teachers",
 *     summary="إضافة مدرس جديد",
 *     tags={"Teachers"},
 *     security={{"sanctum":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","institute_branch_id","hire_date"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="أحمد محمد"),
 *             @OA\Property(property="institute_branch_id", type="integer", example=1),
 *             @OA\Property(property="phone", type="string", example="+963123456789"),
 *             @OA\Property(property="specialization", type="string", example="برمجة"),
 *             @OA\Property(property="hire_date", type="string", format="date", example="2023-01-01")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="تم إنشاء المدرس بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="تم إنشاء المدرس بنجاح"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="user_id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="أحمد محمد"),
 *                 @OA\Property(property="phone", type="string", example="+963123456789"),
 *                 @OA\Property(property="specialization", type="string", example="برمجة"),
 *                 @OA\Property(property="hire_date", type="string", format="date", example="2023-01-01"),
 *
 *                 @OA\Property(
 *                     property="institute_branch",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="فرع دمشق")
 *                 ),
 *
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
 *             )
 *         )
 *     )
 * )
 */
public function store(StoreInstructorRequest $request)
{
    $instructor = Instructor::create($request->validated());

    // تحميل الفرع المرتبط
    $instructor->load('instituteBranch');

    return $this->successResponse(
        new InstructorResource($instructor),
        'تم إنشاء المدرس بنجاح',
        201
    );
}

    /**
 * @OA\Get(
 *     path="/api/teachers/{id}",
 *     summary="عرض تفاصيل مدرس محدد",
 *     tags={"Teachers"},
 *     security={{"sanctum":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="معرف المدرس",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="تم جلب بيانات المدرس بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="تم جلب بيانات المدرس بنجاح"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="user_id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="أحمد محمد"),
 *                 @OA\Property(property="phone", type="string", example="+963123456789"),
 *                 @OA\Property(property="specialization", type="string", example="برمجة"),
 *                 @OA\Property(property="hire_date", type="string", format="date", example="2023-01-01"),
 *
 *                 @OA\Property(
 *                     property="institute_branch",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="فرع دمشق")
 *                 ),
 *
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="المدرس غير موجود",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="المدرس غير موجود"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */
public function show($id)
{
    $instructor = Instructor::with('instituteBranch')->find($id);

    if (!$instructor) {
        return $this->error('المدرس غير موجود', 404);
    }

    return $this->successResponse(
        new InstructorResource($instructor),
        'تم جلب بيانات المدرس بنجاح',
        200
    );
}


   /**
 * @OA\Put(
 *     path="/api/teachers/{id}",
 *     summary="تحديث بيانات مدرس",
 *     tags={"Teachers"},
 *     security={{"sanctum":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="معرف المدرس",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\JsonContent(
 *             @OA\Property(property="user_id", type="integer", example=2),
 *             @OA\Property(property="name", type="string", example="محمد علي"),
 *             @OA\Property(property="institute_branch_id", type="integer", example=2),
 *             @OA\Property(property="phone", type="string", example="+963987654321"),
 *             @OA\Property(property="specialization", type="string", example="تصميم"),
 *             @OA\Property(property="hire_date", type="string", format="date", example="2023-02-01")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="تم تحديث بيانات المدرس بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="تم تحديث بيانات المدرس بنجاح"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="user_id", type="integer", example=2),
 *                 @OA\Property(property="name", type="string", example="محمد علي"),
 *                 @OA\Property(property="phone", type="string", example="+963987654321"),
 *                 @OA\Property(property="specialization", type="string", example="تصميم"),
 *                 @OA\Property(property="hire_date", type="string", format="date", example="2023-02-01"),
 *
 *                 @OA\Property(
 *                     property="institute_branch",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=2),
 *                     @OA\Property(property="name", type="string", example="فرع حلب")
 *                 ),
 *
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-02-01T00:00:00.000000Z")
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="المدرس غير موجود",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="المدرس غير موجود"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */

    public function update(UpdateInstructorRequest $request, $id)
    {
        $instructor = Instructor::find($id);

        if (!$instructor) {
            return $this->error('المدرس غير موجود', 404);
        }

        $instructor->update($request->validated());

        return $this->successResponse(
            new InstructorResource($instructor),
            'تم تحديث بيانات المدرس بنجاح',
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/teachers/{id}",
     *     summary="حذف مدرس",
     *     tags={"Teachers"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف المدرس",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف المدرس بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف المدرس بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="المدرس غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="المدرس غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $instructor = Instructor::find($id);

        if (!$instructor) {
            return $this->error('المدرس غير موجود', 404);
        }

        $instructor->delete();

        return $this->successResponse(
            null,
            'تم حذف المدرس بنجاح',
            200
        );
    }
    /**
     * @OA\Post(
     *     path="/api/teachers/{id}/photo",
     *     summary="تحديث الصورة الشخصية للمدرس",
     *     tags={"Teachers"},
     *     security={{"sanctum":{}}},

     *     @OA\Parameter(
     *         name="id", in="path", required=true, description="معرف المدرس",
     *         @OA\Schema(type="integer")
     *     ),

     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="photo",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     ),

     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث صورة المدرس بنجاح"
     *     )
     * )
     */
    public function updatePhoto(UpdateInstructorPhotoRequest $request, $id)
    {
        $instructor = Instructor::find($id);

        if (!$instructor) {
            return $this->error('المدرس غير موجود', 404);
        }

        // حذف الصورة القديمة إن وُجدت
        if ($instructor->profile_photo_url) {
            // استخراج مسار الملف من الرابط الكامل
            $oldPath = str_replace(asset('storage') . '/', '', $instructor->profile_photo_url);

            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        // رفع الصورة الجديدة
        $path = $request->file('photo')->store('instructors/photos', 'public');

        // حفظ رابط الصورة الجديدة
        $instructor->update([
            'profile_photo_url' => asset('storage/' . $path),
        ]);

        return $this->successResponse(
            new InstructorResource($instructor),
            'تم تحديث صورة المدرس بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/teachers/{id}/batches-details",
     *     summary="جلب تفاصيل الدورات التي يدرّسها المدرس",
     *     description="يعيد قائمة بالدورات (Batches) التي يشارك فيها المدرس مع تفاصيل المواد والقاعات المخصصة لكل مادة يدرّسها.",
     *     tags={"Teachers"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف المدرس (Instructor ID)",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب تفاصيل الدورات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="batch_id", type="integer", example=5),
     *                     @OA\Property(property="batch_name", type="string", example="دورة برمجة الويب - مسائي"),
     *                     @OA\Property(property="start_date", type="string", format="date", example="2025-01-01"),
     *                     @OA\Property(property="end_date", type="string", format="date", example="2025-06-30"),
     *                     @OA\Property(property="subjects", type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="subject_id", type="integer", example=10, nullable=true),
     *                             @OA\Property(property="subject_name", type="string", example="Laravel Advanced", nullable=true),
     *                             @OA\Property(property="class_room_id", type="integer", example=3, nullable=true),
     *                             @OA\Property(property="class_room_name", type="string", example="قاعة 201", nullable=true)
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="المدرس غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Instructor not found"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
public function getBatchesDetails(Request $request, $id)
{
    $type = $request->query('type'); // 👈 نقرأ type فقط
    $instructor = Instructor::find($id);

    if (!$instructor) {
        return response()->json([
            'status' => false,
            'message' => 'Instructor not found'
        ], 404);
    }

    /* =================================================
     | ✅ الحالة الخاصة المطلوبة
     | type = subjects
     | البحث من instructor_subjects فقط
     ================================================= */
    if ($type === 'subjects') {

    $instructorSubjects = InstructorSubject::where('instructor_id', $id)
        ->with('subject.academicBranch') // ✅ تحميل الفرع الأكاديمي من المادة
        ->get();

    return response()->json([
        'status' => true,
        'type'   => 'subjects',
        'data'   => $instructorSubjects->map(function ($is) {
            return [
                'instructor_subject_id' => $is->id,
                'subject' => [
                    'id'   => $is->subject->id ?? null,
                    'name' => $is->subject->name ?? null,

                    // ✅ academic branch من subject
                    'academic_branch' => [
                        'id'   => $is->subject->academicBranch->id ?? null,
                        'name' => $is->subject->academicBranch->name ?? null,
                    ],
                ],
            ];
        })->values()
    ]);
}


    /* =================================================
     | 🔁 باقي الكود كما هو بدون أي تغيير (مع إضافة instituteBranch فقط)
     ================================================= */

    // جلب الدورات التي يدرس فيها الأستاذ مع المواد والقاعة + فرع المعهد
    $batches = Batch::whereHas('batchSubjects.instructorSubject', function ($query) use ($id) {
            $query->where('instructor_id', $id);
        })
        ->with([
            'classRoom',
            // 'academicBranch',  // ✅ الفرع الأكاديمي
            'instituteBranch', // ✅ إضافة تحميل الفرع
            'batchSubjects' => function ($query) use ($id) {
                $query->whereHas('instructorSubject', function ($q) use ($id) {
                    $q->where('instructor_id', $id);
                })
                ->with([
            'subject.academicBranch' // ⭐ هنا التغيير المهم
        ]);
            }
        ])
        ->get();

    $result = $batches->map(function ($batch) {
        return [
            'batch_id'    => $batch->id,
            'batch_name'  => $batch->name,
            'start_date'  => $batch->start_date,
            'end_date'    => $batch->end_date,

            'class_room' => [
                'id'   => $batch->classRoom->id ?? null,
                'name' => $batch->classRoom->name ?? null,
            ],

            'subjects' => $batch->batchSubjects->map(function ($bs) use ($batch) {
                return [
                    'batch_subject_id' => $bs->id,
                    'subject_id'       => $bs->subject->id ?? null,
                    'subject_name'     => $bs->subject->name ?? null,
                    'weekly_lessons'   => $bs->weekly_lessons, // إضافة عدد الحصص

                    // ✅ إضافة institute branch داخل كل subject
                    'institute_branch' => [
                        'id'   => $batch->instituteBranch->id ?? null,
                        'name' => $batch->instituteBranch->name ?? null,
                    ],
                     // ✅ academic branch
                    'academic_branch' => [
                        'id'   => $batch->academicBranch->id ?? null,
                        'name' => $batch->academicBranch->name ?? null,
                    ],
                ];
            }),
        ];
    });

    return response()->json([
        'status' => true,
        'data'   => $result
    ]);
}


    /**
 * @OA\Get(
 *     path="/api/teachers/batches-details",
 *     summary="جلب تفاصيل الدورات لجميع المدرسين",
 *     description="يعيد جميع المدرسين مع الدورات التي يدرّسون فيها، مع المواد والقاعات المخصصة لكل مادة.",
 *     tags={"Teachers"},
 *     security={{"sanctum":{}}},
 *
 *     @OA\Response(
 *         response=200,
 *         description="تم جلب البيانات بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="instructor_id", type="integer", example=1),
 *                     @OA\Property(property="instructor_name", type="string", example="أحمد محمد"),
 *                     @OA\Property(property="batches", type="array",
 *                         @OA\Items(
 *                             type="object",
 *                             @OA\Property(property="batch_id", type="integer", example=5),
 *                             @OA\Property(property="batch_name", type="string", example="دورة برمجة الويب"),
 *                             @OA\Property(property="start_date", type="string", format="date"),
 *                             @OA\Property(property="end_date", type="string", format="date"),
 *                             @OA\Property(property="subjects", type="array",
 *                                 @OA\Items(
 *                                     @OA\Property(property="subject_id", type="integer"),
 *                                     @OA\Property(property="subject_name", type="string"),
 *                                     @OA\Property(property="class_room_id", type="integer"),
 *                                     @OA\Property(property="class_room_name", type="string")
 *                                 )
 *                             )
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     )
 * )
 */
public function getAllTeachersBatchesDetails()
{
    $instructors = Instructor::with([
        'instructorSubjects.batchSubjects.batch',
        'instructorSubjects.batchSubjects.subject',
        'instructorSubjects.batchSubjects.classRoom',
    ])->get();

    $result = $instructors->map(function ($instructor) {

        // جميع المواد المرتبطة بالأستاذ داخل الدفعات
        $allBatchSubjects = $instructor->instructorSubjects
            ->flatMap(function ($is) {
                return $is->batchSubjects;
            });

        // تجميع حسب الدفعة
        $batches = $allBatchSubjects
            ->groupBy(fn ($bs) => $bs->batch->id)
            ->map(function ($items) {

                $batch = $items->first()->batch;

                return [
                    'batch_id'   => $batch->id,
                    'batch_name' => $batch->name,
                    'start_date' => $batch->start_date,
                    'end_date'   => $batch->end_date,
                    'subjects'   => $items->map(function ($bs) {
                        return [
                            'subject_id'       => $bs->subject->id ?? null,
                            'subject_name'     => $bs->subject->name ?? null,
                            'weekly_lessons'   => $bs->weekly_lessons, // إضافة عدد الحصص
                            'class_room_id'    => $bs->classRoom->id ?? null,
                            'class_room_name'  => $bs->classRoom->name ?? null,
                        ];
                    })->values()
                ];
            })->values();

        return [
            'instructor_id'   => $instructor->id,
            'instructor_name' => $instructor->full_name ?? $instructor->name,
            'batches'         => $batches,
        ];
    });

    return response()->json([
        'status' => true,
        'data'   => $result
    ]);
}

}
