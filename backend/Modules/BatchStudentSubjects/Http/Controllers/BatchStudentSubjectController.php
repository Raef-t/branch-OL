<?php

namespace Modules\BatchStudentSubjects\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\BatchStudentSubjects\Http\Requests\DestroyBatchStudentSubjectRequest;
use Modules\BatchStudentSubjects\Models\BatchStudentSubject;
use Modules\BatchStudentSubjects\Http\Requests\StoreBatchStudentSubjectRequest;
use Modules\BatchStudentSubjects\Http\Resources\BatchStudentSubjectResource;

use Modules\BatchSubjects\Models\BatchSubject;

class BatchStudentSubjectController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/batch-student-subjects",
     *     summary="ربط مواد لطالب مسجّل في دفعة (طالب جزئي)",
     *     description="
هذا المسار مخصّص لإدارة **تسجيل المواد لطالب ضمن دفعة معيّنة**.

💡 **ما الذي يفعله هذا المسار؟**
- يربط مادة أو أكثر بالطالب داخل الدفعة.
- ينشئ سجلات في جدول batch_student_subjects.
- إذا كان الربط موجودًا مسبقًا → يتم تحديثه بدل التكرار.

🔒 **ما الذي لا يفعله؟**
- لا يسجّل الطالب بالدفعة.
- لا يحدّد ما إذا كان الطالب كامل أو جزئي.
- لا ينشئ مواد أو دفعات.
- لا يعدّل بيانات الطالب الأساسية.

⚠ **ملاحظات منطقية:**
- يجب تحديد مادة واحدة على الأقل.
- لا يمكن تكرار نفس المادة لنفس الطالب (مضمون بقيد unique).
",
     *     tags={"BatchStudentSubjects"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"batch_student_id","batch_subject_ids"},
     *
     *             @OA\Property(
     *                 property="batch_student_id",
     *                 type="integer",
     *                 example=12,
     *                 description="معرّف سجل تسجيل الطالب في الدفعة (batch_student.id)"
     *             ),
     *
     *             @OA\Property(
     *                 property="batch_subject_ids",
     *                 type="array",
     *                 @OA\Items(type="integer", example=5),
     *                 description="قائمة معرّفات مواد الدفعة المراد ربطها بالطالب"
     *             ),
     *
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 nullable=true,
     *                 example="active",
     *                 description="حالة المادة (active | dropped | completed)"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="تم ربط المواد بالطالب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="تم ربط المواد بالطالب بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=5),
     *                     @OA\Property(property="batch_student_id", type="integer", example=12),
     *                     @OA\Property(property="batch_subject_id", type="integer", example=8),
     *                     @OA\Property(property="status", type="string", example="active"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="فشل التحقق من صحة البيانات",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="يجب تحديد مادة واحدة على الأقل"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="سجل الطالب أو المادة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model")
     *         )
     *     )
     * )
     */

    public function store(StoreBatchStudentSubjectRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {

            $validBatchSubjectIds = BatchSubject::whereIn('id', $data['batch_subject_ids'])
                ->pluck('id')
                ->toArray();

            $records = collect($validBatchSubjectIds)->map(function ($batchSubjectId) use ($data) {
                return BatchStudentSubject::updateOrCreate(
                    [
                        'batch_student_id' => $data['batch_student_id'],
                        'batch_subject_id' => $batchSubjectId,
                    ],
                    [
                        'status' => $data['status'] ?? 'active',
                    ]
                );
            });

            // ✅ تحميل العلاقات بشكل صحيح
            $records->each(function ($item) {
                $item->load(['batchStudent', 'batchSubject']);
            });

            return response()->json([
                'message' => 'تم ربط المواد بالطالب بنجاح',
                'data' => BatchStudentSubjectResource::collection($records),
            ], 201);
        });
    }

    /**
     * @OA\Put(
     *     path="/api/batch-student-subjects/{id}",
     *     summary="تحديث حالة مادة لطالب داخل دفعة",
     *     description="
هذا المسار مخصّص **لتحديث حالة مادة واحدة** مرتبطة بطالب داخل دفعة معيّنة
من خلال جدول `batch_student_subjects`.

💡 **ما الذي يفعله هذا المسار؟**
- يقوم بتحديث قيمة `status` لسجل واحد فقط.
- السجل يمثّل علاقة (طالب ↔ مادة) داخل دفعة.
- يُستخدم في حالات مثل:
  - إنهاء مادة (`completed`)
  - إسقاط مادة (`dropped`)
  - إعادة تفعيل مادة (`active`)

🔒 **ما الذي لا يفعله هذا المسار؟**
- لا يغيّر تسجيل الطالب في الدفعة.
- لا يضيف أو يحذف مواد أخرى.
- لا يغيّر حالة الطالب (كامل / جزئي).
- لا يعدّل أي بيانات أكاديمية أو إدارية أخرى.

📌 **قواعد منطقية مهمّة:**
- يتم تحديث سجل واحد فقط في `batch_student_subjects`.
- القيمة الجديدة للحالة يجب أن تكون واحدة من:
  - `active`
  - `dropped`
  - `completed`
- أي قيمة أخرى سيتم رفضها.

⚠ **ملاحظات سلوكية:**
- هذا المسار **غير قابل للتكرار (non-idempotent)**:
  - استدعاؤه بنفس الحالة لا يغيّر شيئًا فعليًا.
- لا يتم إنشاء سجل جديد في أي حال.
",
     *     tags={"BatchStudentSubjects"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرّف سجل المادة المرتبطة بالطالب (batch_student_subjects.id). هذا المعرّف لا يمثّل student_id ولا batch_subject_id.",
     *         @OA\Schema(type="integer", example=8)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="completed",
     *                 description="الحالة الجديدة للمادة. القيم المسموح بها: active | dropped | completed."
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث حالة المادة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="تم تحديث حالة المادة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="بيانات سجل المادة بعد التحديث",
     *                 @OA\Property(property="id", type="integer", example=8),
     *                 @OA\Property(property="batch_student_id", type="integer", example=12),
     *                 @OA\Property(property="batch_subject_id", type="integer", example=5),
     *                 @OA\Property(property="status", type="string", example="completed"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-20T10:45:00Z")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="سجل المادة المرتبطة بالطالب غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [BatchStudentSubject]")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="فشل التحقق من صحة البيانات أو قيمة حالة غير مسموحة",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="قيمة الحالة يجب أن تكون: active أو dropped أو completed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="خطأ غير متوقع أثناء تحديث حالة المادة",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="حدث خطأ غير متوقع أثناء تحديث حالة المادة")
     *         )
     *     )
     * )
     */



    public function updateStatus(StoreBatchStudentSubjectRequest $request, $id)
    {
        $batchStudentSubject = BatchStudentSubject::findOrFail($id);

        $data = $request->validated();

        $batchStudentSubject->update([
            'status' => $data['status'],
        ]);

        return response()->json([
            'message' => 'تم تحديث حالة المادة بنجاح',
            'data' => new BatchStudentSubjectResource(
                $batchStudentSubject->load(['batchStudent', 'batchSubject'])
            ),
        ]);
    }
    /**
     * @OA\Delete(
     *     path="/api/batch-student-subjects/{id}",
     *     summary="حذف مادة من تسجيل الطالب داخل دفعة",
     *     description="
هذا المسار مخصّص **لإلغاء ربط مادة عن طالب داخل دفعة معيّنة**
من خلال حذف السجل المقابل من جدول `batch_student_subjects`.

💡 **ما الذي يفعله هذا المسار؟**
- يحذف سجل الربط بين الطالب والمادة.
- يمنع الطالب من متابعة هذه المادة بعد الحذف.
- العملية تمثّل **إلغاء تسجيل مادة واحدة فقط**.

🔒 **ما الذي لا يفعله هذا المسار؟**
- لا يحذف الطالب.
- لا يحذف الدفعة.
- لا يحذف المادة نفسها.
- لا يغيّر تسجيل الطالب في الدفعة (كامل / جزئي).
- لا يؤثر على أي مواد أخرى مسجّلة للطالب.

📌 **قواعد منطقية مهمّة:**
- يتم حذف سجل واحد فقط محدّد بالـ `id`.
- لا يمكن حذف مادة غير موجودة أو غير مرتبطة بالطالب.

⚠ **ملاحظات سلوكية:**
- هذا الحذف **نهائي (Hard Delete)**.
- لا يمكن التراجع عنه بعد التنفيذ.
- أي محاولات لاحقة على نفس الـ `id` ستفشل.
",
     *     tags={"BatchStudentSubjects"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرّف سجل المادة المرتبطة بالطالب (batch_student_subjects.id). هذا المعرّف لا يمثّل student_id ولا batch_subject_id.",
     *         @OA\Schema(type="integer", example=8)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف المادة من الطالب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="تم حذف المادة من الطالب بنجاح"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="سجل المادة المراد حذفه غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="No query results for model [BatchStudentSubject]"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="خطأ غير متوقع أثناء حذف المادة",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="حدث خطأ غير متوقع أثناء حذف المادة"
     *             )
     *         )
     *     )
     * )
     */


    public function destroy(DestroyBatchStudentSubjectRequest $request, $id)
    {
        $record = $request->record();

        if (! $record) {
            return response()->json([
                'status'  => 'error',
                'code'    => 'BATCH_STUDENT_SUBJECT_NOT_FOUND',
                'message' => 'لا يمكن حذف المادة لأنها غير موجودة أو محذوفة مسبقًا',
                'errors'  => $request->errors(),
            ], 404);
        }

        $record->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'تم حذف المادة من تسجيل الطالب بنجاح',
        ]);
    }
}
