<?php

namespace Modules\BatchSubjects\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\BatchSubjects\Models\BatchSubject;
use Modules\InstructorSubjects\Http\Resources\InstructorSubjectResource;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\BatchSubjects\Http\Requests\AssignSubjectToBatchRequest;
use Modules\Instructors\Http\Resources\InstructorResource;
use Modules\Batches\Models\Batch;
use Illuminate\Support\Facades\DB;
use Modules\ExamResults\Models\ExamResult;
use Modules\InstructorSubjects\Models\InstructorSubject;

class BatchSubjectsController extends Controller
{
    use SuccessResponseTrait;

    /**
     * روت أساسي: جلب المواد المتعلقة بدورة معينة
     * @OA\Get(
     *     path="/api/batcheSubjects/{batch}/subjects",
     *     summary="جلب المواد المتعلقة بدورة معينة",
     *     tags={"BatchSubjects"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="batch",
     *         in="path",
     *         required=true,
     *         description="معرف الدورة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب المواد بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب المواد المتعلقة بالدورة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="subject_name", type="string", example="رياضيات"),
     *                     @OA\Property(property="instructor_name", type="string", example="أحمد محمد")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا توجد مواد لهذه الدورة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا توجد مواد متعلقة بهذه الدورة حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function getSubjectsByBatch($batchId)
    {
        $batchSubjects = BatchSubject::with(['instructorSubject.instructor', 'instructorSubject.subject'])
            ->where('batch_id', $batchId)
            ->where('is_active', true)
            ->get();

        if ($batchSubjects->isEmpty()) {
            return $this->error('لا توجد مواد متعلقة بهذه الدورة حالياً', 404);
        }

        $subjects = $batchSubjects->map(function ($item) {
            return [
                'id' => $item->id,
                'subject_name' => $item->instructorSubject->subject->name ?? 'غير محدد',
                'instructor_name' => $item->instructorSubject->instructor->name ?? 'غير محدد',
                'notes' => $item->notes,
            ];
        });

        return $this->successResponse(
            $subjects,
            'تم جلب المواد المتعلقة بالدورة بنجاح',
            200
        );
    }

    /**
     * روت أساسي: إضافة المواد التابعة لدورة معينة
     * @OA\Post(
     *     path="/api/batcheSubjects/assign-instructor-subject", 
     *     summary="إضافة أستاذ مادة تابعة لدورة معينة",
     *     tags={"BatchSubjects"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"batch_id","instructor_subject_id"},
     *             @OA\Property(property="batch_id", type="integer", example=1),
     *             @OA\Property(property="instructor_subject_id", type="integer", example=1),
     *             @OA\Property(property="notes", type="string", example="ملاحظات")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم الإضافة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إضافة المادة للدورة بنجاح"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="batch_id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="التخصيص موجود مسبقاً",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="التخصيص موجود مسبقاً")
     *         )
     *     )
     * )
     */
    public function assignInstructorSubjectToBatch(AssignSubjectToBatchRequest $request)
    {
        // تحقق من وجود الـ InstructorSubject
        $instructorSubject = InstructorSubject::find($request->instructor_subject_id);
        if (!$instructorSubject) {
            return $this->error('الأستاذ المادة غير موجود', 404);
        }

        // تحقق من وجود تخصيص مسبق
        $exists = BatchSubject::where('batch_id', $request->batch_id)
            ->where('instructor_subject_id', $request->instructor_subject_id)
            ->first();

        if ($exists) {
            return $this->error('التخصيص موجود مسبقاً', 409);
        }

        // إنشاء التخصيص
        $assignment = BatchSubject::create([
            'batch_id' => $request->batch_id,
            'instructor_subject_id' => $request->instructor_subject_id,
            'weekly_lessons' => $request->weekly_lessons, // إضافة عدد الحصص
            'assigned_by' => Auth::id(), // ID المستخدم الحالي
            'assignment_date' => now(),
            'notes' => $request->notes,
            'is_active' => true,
            'subject_id' => $instructorSubject->subject_id, // جلب المادة تلقائياً من InstructorSubject
        ]);

        return $this->successResponse(
            $assignment->load(['batch', 'instructorSubject']),
            'تم إضافة المادة للدورة بنجاح',
            201
        );
    }

    /**
     * روت أساسي: إلغاء تخصيص أستاذ مادة من دورة (إزالة الربط بين الدورة والـ instructor_subject)
     * @OA\Patch(
     *     path="/api/batcheSubjects/remove-instructor-subject",
     *     summary="إلغاء تخصيص أستاذ مادة من دورة معينة",
     *     description="يُستخدم هذا المسار لإلغاء ربط مادة معينة مع أستاذها في دورة محددة، عن طريق تعيين instructor_subject_id إلى null في سجل batch_subjects المقابل. هذا يعني إزالة الأستاذ المكلّف بالمادة في هذه الدورة دون حذف السجل كاملاً.",
     *     tags={"BatchSubjects"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"batch_id","instructor_subject_id"},
     *             @OA\Property(property="batch_id", type="integer", example=1, description="معرف الدورة"),
     *             @OA\Property(property="instructor_subject_id", type="integer", example=1, description="معرف ربط الأستاذ بالمادة (instructor_subjects.id)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم إلغاء التخصيص بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إلغاء التخصيص بنجاح"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="batch_id", type="integer", example=1),
     *                 @OA\Property(property="instructor_subject_id", type="null"),
     *                 @OA\Property(property="batch", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="التخصيص غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="التخصيص غير موجود")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="بيانات الطلب غير صالحة",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function removeInstructorSubjectFromBatch(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|integer|exists:batches,id',
            'instructor_subject_id' => 'required|integer|exists:instructor_subjects,id',
        ]);

        // جلب التخصيص الحالي إذا موجود
        $assignment = BatchSubject::where('batch_id', $request->batch_id)
            ->where('instructor_subject_id', $request->instructor_subject_id)
            ->first();

        if (!$assignment) {
            return $this->error('التخصيص غير موجود', 404);
        }

        // إزالة التخصيص عن طريق تعيين instructor_subject_id إلى null
        $assignment->update([
            'instructor_subject_id' => null
        ]);

        return $this->successResponse(
            $assignment->load(['batch']),
            'تم إلغاء التخصيص بنجاح',
            200
        );
    }

    /**
     * التحقق من وجود تخصيص
     * @OA\Post(
     *     path="/api/batcheSubjects/check-subject-assignment",
     *     summary="التحقق من وجود تخصيص مادة لدورة",
     *     tags={"BatchSubjects"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"batch_id","instructor_subject_id"},
     *             @OA\Property(property="batch_id", type="integer", example=1),
     *             @OA\Property(property="instructor_subject_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم التحقق بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم التحقق بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="exists", type="boolean", example=false)
     *             )
     *         )
     *     )
     * )
     */
    public function checkSubjectAssignment(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|integer|exists:batches,id',
            'instructor_subject_id' => 'required|integer|exists:instructor_subjects,id',
        ]);

        $exists = BatchSubject::where('batch_id', $request->batch_id)
            ->where('instructor_subject_id', $request->instructor_subject_id)
            ->exists();

        return $this->successResponse(
            ['exists' => $exists],
            'تم التحقق بنجاح',
            200
        );
    }

    /**
     * تعديل تخصيص
     * @OA\Put(
     *     path="/api/batcheSubjects/update-batch-subject/{id}",
     *     summary="تعديل تخصيص مادة لدورة",
     *     tags={"BatchSubjects"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف التخصيص",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"instructor_subject_id"},
     *             @OA\Property(property="instructor_subject_id", type="integer", example=2),
     *             @OA\Property(property="notes", type="string", example="ملاحظات جديدة")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم التعديل بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تعديل التخصيص بنجاح"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="التخصيص غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="التخصيص غير موجود")
     *         )
     *     )
     * )
     */
    public function updateBatchSubject(AssignSubjectToBatchRequest $request, $id)
    {
        $assignment = BatchSubject::find($id);

        if (!$assignment) {
            return $this->error('التخصيص غير موجود', 404);
        }

        $assignment->update($request->validated());

        return $this->successResponse(
            $assignment->load(['batch', 'instructorSubject']),
            'تم تعديل التخصيص بنجاح',
            200
        );
    }

    /**
     * حذف تخصيص
     * @OA\Delete(
     *     path="/api/batcheSubjects/delete-batch-subject/{id}",
     *     summary="حذف تخصيص مادة لدورة",
     *     tags={"BatchSubjects"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف التخصيص",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم الحذف بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف التخصيص بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="التخصيص غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="التخصيص غير موجود")
     *         )
     *     )
     * )
     */
    public function deleteBatchSubject($id)
    {
        $assignment = BatchSubject::find($id);

        if (!$assignment) {
            return $this->error('التخصيص غير موجود', 404);
        }

        $assignment->delete();

        return $this->successResponse(
            null,
            'تم حذف التخصيص بنجاح',
            200
        );
    }

    /**
     * إلغاء تفعيل تخصيص
     * @OA\Patch(
     *     path="/api/batcheSubjects/deactivate-batch-subject/{id}",
     *     summary="إلغاء تفعيل تخصيص مادة لدورة",
     *     tags={"BatchSubjects"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف التخصيص",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم إلغاء التفعيل بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إلغاء تفعيل التخصيص بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="التخصيص غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="التخصيص غير موجود")
     *         )
     *     )
     * )
     */
    public function deactivateBatchSubject($id)
    {
        $assignment = BatchSubject::find($id);

        if (!$assignment) {
            return $this->error('التخصيص غير موجود', 404);
        }

        $assignment->update(['is_active' => false]);

        return $this->successResponse(
            null,
            'تم إلغاء تفعيل التخصيص بنجاح',
            200
        );
    }

    /**
     * جلب جميع التخصيصات النشطة
     * @OA\Get(
     *     path="/api/batcheSubjects/subjects/all",
     *     summary="جلب جميع التخصيصات النشطة",
     *     tags={"BatchSubjects"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع التخصيصات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع التخصيصات بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function getAllActiveAssignments()
    {
        $assignments = BatchSubject::with([
            'batch',
            'instructorSubject.instructor',
            'instructorSubject.subject'
        ])
            ->where('is_active', true)
            ->paginate(20); // يفضل استخدام pagination

        return $this->successResponse(
            \Modules\BatchSubjects\Http\Resources\BatchSubjectResource::collection($assignments),
            'تم جلب جميع التخصيصات بنجاح',
            200
        );
    }


    /**
     * جلب تخصيصات لمدرس معين
     * @OA\Get(
     *     path="/api/batcheSubjects/instructors/{instructor}/assignments",
     *     summary="جلب تخصيصات لمدرس معين",
     *     tags={"BatchSubjects"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="instructor",
     *         in="path",
     *         required=true,
     *         description="معرف المدرس",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب التخصيصات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب تخصيصات المدرس بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function getAssignmentsByInstructor($instructorId)
    {
        $assignments = BatchSubject::with(['batch', 'instructorSubject.subject'])
            ->whereHas('instructorSubject', function ($query) use ($instructorId) {
                $query->where('instructor_id', $instructorId);
            })
            ->where('is_active', true)
            ->get();

        return $this->successResponse(
            $assignments,
            'تم جلب تخصيصات المدرس بنجاح',
            200
        );
    }

    /**
     * جلب تخصيصات لمادة معينة
     * @OA\Get(
     *     path="/api/batcheSubjects/subjects/{subject}/assignments",
     *     summary="جلب تخصيصات لمادة معينة عبر الدورات",
     *     tags={"BatchSubjects"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="subject",
     *         in="path",
     *         required=true,
     *         description="معرف المادة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب التخصيصات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب تخصيصات المادة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function getAssignmentsBySubject($subjectId)
    {
        $assignments = BatchSubject::with(['batch', 'instructorSubject.instructor'])
            ->whereHas('instructorSubject', function ($query) use ($subjectId) {
                $query->where('subject_id', $subjectId);
            })
            ->where('is_active', true)
            ->get();

        return $this->successResponse(
            $assignments,
            'تم جلب تخصيصات المادة بنجاح',
            200
        );
    }

    /**
     * جلب مدرسين لدورة معينة (عبر المواد)
     * @OA\Get(
     *     path="/api/batcheSubjects/{batch}/instructors",
     *     summary="جلب المدرسين المتعلقين بدورة معينة",
     *     tags={"BatchSubjects"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="batch",
     *         in="path",
     *         required=true,
     *         description="معرف الدورة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب المدرسين بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب المدرسين للدورة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد مدرسون لهذه الدورة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد مدرسون لهذه الدورة")
     *         )
     *     )
     * )
     */
    public function getInstructorsByBatch($batchId)
    {
        $batchSubjects = BatchSubject::with('instructorSubject.instructor')
            ->where('batch_id', $batchId)
            ->where('is_active', true)
            ->get();

        if ($batchSubjects->isEmpty()) {
            return $this->error('لا يوجد مدرسون لهذه الدورة', 404);
        }

        $instructors = $batchSubjects->pluck('instructorSubject.instructor')->unique('id');

        return $this->successResponse(
            InstructorResource::collection($instructors),
            'تم جلب المدرسين للدورة بنجاح',
            200
        );
    }

    /**
     * جلب مواد لشعبة معينة (عبر الدورات التابعة)
     * @OA\Get(
     *     path="/api/batcheSubjects/branches/{branch}/subjects",
     *     summary="جلب المواد المتعلقة بشعبة معينة",
     *     tags={"BatchSubjects"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="branch",
     *         in="path",
     *         required=true,
     *         description="معرف الشعبة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب المواد بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب المواد للشعبة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا توجد مواد لهذه الشعبة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا توجد مواد لهذه الشعبة")
     *         )
     *     )
     * )
     */
    public function getSubjectsByBranch($branchId)
    {
        $batches = Batch::where('institute_branch_id', $branchId)->pluck('id');
        $batchSubjects = BatchSubject::with(['instructorSubject.subject'])
            ->whereIn('batch_id', $batches)
            ->where('is_active', true)
            ->get();

        if ($batchSubjects->isEmpty()) {
            return $this->error('لا توجد مواد لهذه الشعبة', 404);
        }

        $subjects = $batchSubjects->pluck('instructorSubject.subject')->unique('id');

        return $this->successResponse(
            $subjects,
            'تم جلب المواد للشعبة بنجاح',
            200
        );
    }

    /**
     * إحصائيات بسيطة لدورة (عدد المواد، عدد المدرسين)
     * @OA\Get(
     *     path="/api/batcheSubjects/{batch}/stats",
     *     summary="جلب إحصائيات لدورة معينة",
     *     tags={"BatchSubjects"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="batch",
     *         in="path",
     *         required=true,
     *         description="معرف الدورة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب الإحصائيات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب الإحصائيات بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="subjects_count", type="integer", example=5),
     *                 @OA\Property(property="instructors_count", type="integer", example=3)
     *             )
     *         )
     *     )
     * )
     */
    public function getBatchStats($batchId)
    {
        $stats = DB::table('batch_subjects')
            ->where('batch_id', $batchId)
            ->where('is_active', true)
            ->selectRaw('COUNT(DISTINCT instructor_subject_id) as subjects_count, COUNT(DISTINCT instructor_subjects.instructor_id) as instructors_count')
            ->join('instructor_subjects', 'batch_subjects.instructor_subject_id', '=', 'instructor_subjects.id')
            ->first();

        return $this->successResponse(
            $stats ?? ['subjects_count' => 0, 'instructors_count' => 0],
            'تم جلب الإحصائيات بنجاح',
            200
        );
    }


    /**
     * @OA\Get(
     *     path="/api/batcheSubjects/summary",
     *     summary="جلب ملخص جميع تعيينات المواد على الدفعات (Batch Subjects Summary)",
     *     description="
    هذا المسار مخصّص لجلب **قائمة مجمّعة** من جميع تعيينات المواد على الدفعات (batch_subjects)،
    بصيغة مبسّطة ومناسبة للاستخدام المباشر في الواجهة الأمامية (Frontend).

    💡 **ما الهدف من هذا المسار؟**
    - تزويد الفرونت بجميع خيارات `batch_subject_id` المتاحة.
    - تمكين الفرونت من:
    - عرض اسم الدورة (الدفعة).
    - عرض اسم المادة.
    - عرض اسم المادة مع اسم الأستاذ.
    - استخدام `batch_subject_id` لاحقًا في عمليات:
    - الفلترة
    - الربط
    - الحفظ
    - إنشاء سجلات مرتبطة (مثل: جداول، امتحانات، حضور، إلخ).

    📤 **ما الذي يعيده هذا المسار؟**
    - قائمة (Array) من العناصر، كل عنصر يمثل سجلًا واحدًا من batch_subjects مع بيانات مشتقة من عدة جداول:
    - batches
    - subjects
    - instructor_subjects
    - instructors

    🔒 **ملاحظات مهمة:**
    - لا يحتاج هذا المسار إلى أي Parameters.
    - لا يقوم بأي تعديل على البيانات.
    - مناسب للاستدعاء المتكرر (Safe & Idempotent).
    ",
     *     tags={"BatchSubjects"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب قائمة تعيينات المواد على الدفعات بنجاح",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(
     *                     property="batch_subject_id",
     *                     type="integer",
     *                     example=15,
     *                     description="المعرّف الفريد لسجل تعيين المادة على الدفعة (batch_subjects.id)"
     *                 ),
     *                 @OA\Property(
     *                     property="batch_name",
     *                     type="string",
     *                     example="Batch A - 2025",
     *                     description="اسم الدورة / الدفعة المرتبطة بهذا التعيين"
     *                 ),
     *                 @OA\Property(
     *                     property="subject_name",
     *                     type="string",
     *                     example="Mathematics",
     *                     description="اسم المادة المرتبطة بهذا التعيين"
     *                 ),
     *                 @OA\Property(
     *                     property="subject_instructor_name",
     *                     type="string",
     *                     example="Mathematics - Ahmad Ali",
     *                     description="اسم المادة مدموج مع اسم الأستاذ المكلّف بتدريسها"
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح – المستخدم غير مسجل دخول",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="خطأ غير متوقع في الخادم أثناء جلب البيانات",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="حدث خطأ غير متوقع أثناء جلب بيانات batch subjects")
     *         )
     *     )
     * )
     */
    public function getBatchSubjectsSummary()
    {
        $batchSubjects = BatchSubject::with([
            'batch:id,name',
            'subject:id,name',
            'instructorSubject.instructor:id,name',
        ])->get();

        $data = $batchSubjects->map(function ($batchSubject) {
            return [
                'batch_subject_id' => $batchSubject->id,
                'batch_name'       => $batchSubject->batch?->name,
                'subject_name'     => $batchSubject->subject?->name,
                'subject_instructor_name' =>
                $batchSubject->subject?->name
                    . ' - '
                    . $batchSubject->instructorSubject?->instructor?->name,
            ];
        });

        return $this->successResponse(
            $data,
            'تم جلب ملخص المواد المرتبطة بالدفعات بنجاح',
            200
        );
    }
}
