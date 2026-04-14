<?php

namespace Modules\BatchStudents\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Modules\BatchStudents\Models\BatchStudent;
use Modules\Batches\Models\Batch;
use Modules\ClassRooms\Models\ClassRoom;
use Modules\Students\Models\Student;
use Modules\BatchStudents\Http\Requests\StoreBatchStudentRequest;
use Modules\BatchStudents\Http\Requests\UpdateBatchStudentRequest;
use Modules\BatchStudents\Http\Resources\BatchStudentResource;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\BatchStudents\Http\Resources\StudentWithRemainingAmountResource;

class BatchStudentsController extends Controller
{
    use SuccessResponseTrait;



    /**
     * @OA\Get(
     * path="/api/batch-students",
     * summary="قائمة جميع تسجيلات الطلاب في الدفعات",
     * tags={"Batch Students"},
     * security={{"sanctum":{}}},
     * @OA\Response(
     * response=200,
     * description="تم جلب جميع تسجيلات الطلاب في الدفعات بنجاح",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="تم جلب جميع تسجيلات الطلاب في الدفعات بنجاح"),
     * @OA\Property(
     * property="data",
     * type="array",
     * @OA\Items(
     * type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="batch_id", type="integer", example=1),
     * @OA\Property(property="student_id", type="integer", example=1),
     * @OA\Property(property="class_room_id", type="integer", example=1),
     * @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     * @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     * )
     * )
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="لا يوجد تسجيلات",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="boolean", example=false),
     * @OA\Property(property="message", type="string", example="لا يوجد أي تسجيل لطلاب في الدفعات حالياً"),
     * @OA\Property(property="data", type="null")
     * )
     * )
     * )
     */
    public function index()
    {
        $batchStudents = BatchStudent::all();
        if ($batchStudents->isEmpty()) {
            return $this->error('لا يوجد أي تسجيل لطلاب في الدفعات حالياً', 404);
        }
        return $this->successResponse(
            BatchStudentResource::collection($batchStudents),
            'تم جلب جميع تسجيلات الطلاب في الدفعات بنجاح',
            200
        );
    }
    /**
     * @OA\Post(
     * path="/api/batch-students",
     * summary="إضافة تسجيل طالب في دفعة جديد",
     * tags={"Batch Students"},
     * security={{"sanctum":{}}},
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         required={"batch_id","student_id"},
     *         @OA\Property(property="batch_id", type="integer", example=1),
     *         @OA\Property(property="student_id", type="integer", example=1)
     *     )
     * ),
     * @OA\Response(
     *     response=201,
     *     description="تم إنشاء تسجيل الطالب في الدفعة بنجاح",
     *     @OA\JsonContent(
     *         @OA\Property(property="status", type="boolean", example=true),
     *         @OA\Property(property="message", type="string", example="تم إنشاء تسجيل الطالب في الدفعة بنجاح"),
     *         @OA\Property(
     *             property="data",
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="batch_id", type="integer", example=1),
     *             @OA\Property(property="student_id", type="integer", example=1),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *         )
     *     )
     * )
     * )
     */
    public function store(StoreBatchStudentRequest $request)
    {
        $batchStudent = BatchStudent::create($request->validated());
        return $this->successResponse(
            new BatchStudentResource($batchStudent),
            'تم إنشاء تسجيل الطالب في الدفعة بنجاح',
            201
        );
    }
    /**
     * @OA\Get(
     *     path="/api/batch-students/{id}",
     *     summary="عرض تفاصيل تسجيل طالب في دفعة (كامل أو جزئي)",
     *     description="
هذا المسار مخصّص **لعرض تفاصيل تسجيل طالب داخل دفعة معيّنة**،  
ويقوم بإيضاح **نوع التسجيل بشكل صريح وواضح** دون الحاجة إلى استنتاجات من المستهلك.

🔹 **أنواع التسجيل المدعومة:**

1️⃣ **تسجيل كامل (Full Enrollment):**
- الطالب مسجّل إداريًا في الدفعة كاملة.
- لا توجد له مواد محددة في جدول `batch_student_subjects`.
- يتم التصريح في الاستجابة أن الطالب:
  - `enrollment_type = full`
  - ولا يتم إرجاع قائمة مواد فردية.

2️⃣ **تسجيل جزئي (Partial Enrollment):**
- الطالب مسجّل في الدفعة لكن لبعض المواد فقط.
- توجد سجلات مرتبطة في جدول `batch_student_subjects`.
- يتم إرجاع:
  - `enrollment_type = partial`
  - قائمة المواد المسجّل بها الطالب مع حالة كل مادة.

💡 **مبدأ مهم:**
- هذا المسار **لا يختلق مواد للطالب الكامل**.
- عدم وجود مواد في الاستجابة **يعني صراحة أن الطالب مسجّل بالدفعة كاملة**.

🔒 **ما الذي لا يفعله هذا المسار؟**
- لا ينشئ تسجيلًا جديدًا.
- لا يعدّل نوع تسجيل الطالب.
- لا يربط أو يفصل مواد.
- لا يغيّر بيانات الطالب أو الدفعة.

📌 **الاستخدام النموذجي:**
- شاشات عرض تفاصيل الطالب.
- تحديد صلاحيات الطالب الأكاديمية.
- بناء الجداول والحضور والامتحانات بناءً على نوع التسجيل.
",
     *     tags={"Batch Students"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرّف سجل تسجيل الطالب في الدفعة (batch_student.id). هذا المعرّف يمثّل عملية الالتحاق نفسها وليس student_id.",
     *         @OA\Schema(type="integer", example=12)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات تسجيل الطالب في الدفعة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=12),
     *             @OA\Property(property="student_id", type="integer", example=5),
     *             @OA\Property(property="batch_id", type="integer", example=3),
     *
     *             @OA\Property(
     *                 property="enrollment_type",
     *                 type="string",
     *                 example="full",
     *                 description="نوع تسجيل الطالب في الدفعة: full = تسجيل كامل، partial = تسجيل جزئي"
     *             ),
     *
     *             @OA\Property(
     *                 property="enrollment_description",
     *                 type="string",
     *                 example="الطالب مسجّل بالدفعة كاملة",
     *                 description="وصف بشري واضح لحالة تسجيل الطالب"
     *             ),
     *
     *             @OA\Property(
     *                 property="subjects",
     *                 type="array",
     *                 nullable=true,
     *                 description="قائمة المواد المسجّل بها الطالب. تظهر **فقط** إذا كان التسجيل جزئيًا (partial).",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="batch_subject_id", type="integer", example=8),
     *                     @OA\Property(property="status", type="string", example="active")
     *                 )
     *             ),
     *
     *             @OA\Property(
     *                 property="has_custom_subjects",
     *                 type="boolean",
     *                 example=false,
     *                 description="يشير إلى ما إذا كان الطالب مسجّلًا بمواد مخصّصة (true للجزئي، false للكامل)"
     *             ),
     *
     *             @OA\Property(
     *                 property="created_at",
     *                 type="string",
     *                 format="date-time",
     *                 example="2025-01-15 10:30:00"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="سجل تسجيل الطالب في الدفعة غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="string", example="BATCH_STUDENT_NOT_FOUND"),
     *             @OA\Property(property="message", type="string", example="تسجيل الطالب في الدفعة غير موجود")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح بالوصول (المستخدم غير مسجل دخول)",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="خطأ غير متوقع أثناء جلب بيانات تسجيل الطالب",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="حدث خطأ غير متوقع أثناء جلب بيانات تسجيل الطالب")
     *         )
     *     )
     * )
     */


    public function show($id)
    {
        $batchStudent = BatchStudent::with([
            'batch.batchSubjects.subject',
            'student',
            'batchSubjects.batchSubject.subject',
        ])->find($id);

        if (! $batchStudent) {
            return response()->json([
                'status'  => 'error',
                'code'    => 'BATCH_STUDENT_NOT_FOUND',
                'message' => 'تسجيل الطالب في الدفعة غير موجود',
            ], 404);
        }

        return new BatchStudentResource($batchStudent);
    }
    /**
     * @OA\Put(
     * path="/api/batch-students/{id}",
     * summary="تحديث بيانات تسجيل طالب في دفعة",
     * tags={"Batch Students"},
     * security={{"sanctum":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="معرف التسجيل",
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\RequestBody(
     * required=false,
     * @OA\JsonContent(
     * @OA\Property(property="batch_id", type="integer", example=2),
     * @OA\Property(property="student_id", type="integer", example=2),
     * @OA\Property(property="class_room_id", type="integer", example=2)
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="تم تحديث بيانات التسجيل بنجاح",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="تم تحديث بيانات التسجيل بنجاح"),
     * @OA\Property(
     * property="data",
     * type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="batch_id", type="integer", example=2),
     * @OA\Property(property="student_id", type="integer", example=2),
     * @OA\Property(property="class_room_id", type="integer", example=2),
     * @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     * @OA\Property(property="updated_at", type="string", format="date-time", example="2023-02-01T00:00:00.000000Z")
     * )
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="التسجيل غير موجود",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="boolean", example=false),
     * @OA\Property(property="message", type="string", example="التسجيل غير موجود"),
     * @OA\Property(property="data", type="null")
     * )
     * )
     * )
     */
    public function update(UpdateBatchStudentRequest $request, $id)
    {
        $batchStudent = BatchStudent::find($id);
        if (!$batchStudent) {
            return $this->error('التسجيل غير موجود', 404);
        }
        $batchStudent->update($request->validated());
        return $this->successResponse(
            new BatchStudentResource($batchStudent),
            'تم تحديث بيانات التسجيل بنجاح',
            200
        );
    }
    /**
     * @OA\Delete(
     * path="/api/batch-students/{id}",
     * summary="حذف تسجيل طالب في دفعة",
     * tags={"Batch Students"},
     * security={{"sanctum":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="معرف التسجيل",
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="تم حذف التسجيل بنجاح",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="تم حذف التسجيل بنجاح"),
     * @OA\Property(property="data", type="null")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="التسجيل غير موجود",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="boolean", example=false),
     * @OA\Property(property="message", type="string", example="التسجيل غير موجود"),
     * @OA\Property(property="data", type="null")
     * )
     * )
     * )
     */
    public function destroy($id)
    {
        $batchStudent = BatchStudent::find($id);
        if (!$batchStudent) {
            return $this->error('التسجيل غير موجود', 404);
        }
        $batchStudent->delete();
        return $this->successResponse(null, 'تم حذف التسجيل بنجاح', 200);
    }

    /**
     * @OA\Get(
     *     path="/api/batch-students/{batch_id}/students",
     *     summary="جلب طلاب دفعة معينة مع المبلغ المتبقي ووقت تسجيل الطالب",
     *     tags={"Batch Students"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="batch_id",
     *         in="path",
     *         required=true,
     *         description="معرف الدفعة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب البيانات بنجاح",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب طلاب الشعبة مع المبلغ المتبقي ووقت التسجيل بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="قائمة طلاب الدفعة مع المبلغ المتبقي ووقت تسجيل الطالب",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"id","first_name","last_name","full_name","gender","attendance_enrolment"},
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=5),
     *                     @OA\Property(property="first_name", type="string", example="محمد"),
     *                     @OA\Property(property="last_name", type="string", example="أحمد"),
     *                     @OA\Property(property="full_name", type="string", example="محمد أحمد"),
     *                     @OA\Property(property="gender", type="string", example="male"),
     *                     @OA\Property(property="profile_photo_url", type="string", example="https://example.com/photos/1.jpg"),
     *                     @OA\Property(property="attendance_enrolment", type="string", format="date", example="2025-01-05"),
     *                     @OA\Property(property="remaining_amount_usd", type="number", example=600)
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="الشعبة غير موجودة أو لا يوجد طلاب",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الشعبة غير موجودة أو لا يوجد طلاب"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function studentsByBatch($batch_id)
    {
        $batch = Batch::find($batch_id);
        if (!$batch) {
            return $this->error('الشعبة غير موجودة', 404);
        }

        $today = Carbon::today()->toDateString();

        $batchStudents = BatchStudent::with([
            'student.latestActiveEnrollmentContract',
            'student.family.guardians.primaryPhone',
            'student.attendances' => function ($q) {
                $q->whereDate('attendance_date', now()->toDateString());
            }
        ])
            ->where('batch_id', $batch_id)
            ->get();

        if ($batchStudents->isEmpty()) {
            return $this->error('لا يوجد طلاب مرتبطين بالشعبة', 404);
        }

        return $this->successResponse(
            StudentWithRemainingAmountResource::collection($batchStudents),
            'تم جلب طلاب الشعبة مع المبلغ المتبقي وحالة الحضور لليوم بنجاح',
            200
        );
    }
}
