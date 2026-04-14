<?php

namespace Modules\InstructorSubjects\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Instructors\Http\Resources\InstructorResource;
use Modules\InstructorSubjects\Models\InstructorSubject;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\InstructorSubjects\Http\Requests\AssignInstructorToSubjectRequest;

class InstructorSubjectsController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/subjects/{subject}/teachers",
     *     summary="جلب جميع المدرسين الذين يدرسون مادة معينة",
     *     tags={"InstructorSubjects"},
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
     *         description="تم جلب جميع المدرسين بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب المدرسين المرتبطين بالمادة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="أحمد محمد"),
     *                     @OA\Property(property="phone", type="string", example="+963123456789"),
     *                     @OA\Property(property="specialization", type="string", example="برمجة")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد أي مدرس مرتبط بهذه المادة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي مدرس مرتبط بهذه المادة حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function getInstructorsBySubject($subjectId)
    {
        $instructorSubjects = InstructorSubject::with('instructor')
            ->where('subject_id', $subjectId)
            ->where('is_active', true)
            ->get();

        if ($instructorSubjects->isEmpty()) {
            return $this->error('لا يوجد أي مدرس مرتبط بهذه المادة حالياً', 404);
        }

        $instructors = $instructorSubjects->pluck('instructor');

        return $this->successResponse(
            InstructorResource::collection($instructors),
            'تم جلب المدرسين المرتبطين بالمادة بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/subjects/assign-teacher",
     *     summary="ربط مدرس بمادة معينة",
     *     tags={"InstructorSubjects"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"subject_id","instructor_id"},
     *             @OA\Property(property="subject_id", type="integer", example=1),
     *             @OA\Property(property="instructor_id", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم ربط المدرس بالمادة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم ربط المدرس بالمادة بنجاح"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="subject_id", type="integer", example=1),
     *                 @OA\Property(property="instructor_id", type="integer", example=3),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-30T08:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-30T08:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="العلاقة موجودة مسبقاً",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="العلاقة موجودة مسبقاً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function assignInstructorToSubject(AssignInstructorToSubjectRequest $request)
    {
        $exists = InstructorSubject::where('subject_id', $request->subject_id)
            ->where('instructor_id', $request->instructor_id)
            ->first();

        if ($exists) {
            return $this->error('العلاقة موجودة مسبقاً', 409);
        }

        $relation = InstructorSubject::create([
            'subject_id' => $request->subject_id,
            'instructor_id' => $request->instructor_id,
            'is_active' => true,
        ]);

        return $this->successResponse(
            $relation,
            'تم ربط المدرس بالمادة بنجاح',
            201
        );
    }

    /**
     * @OA\Put(
     *     path="/api/subjects/update-teacher-subject/{id}",
     *     summary="تعديل ربط مدرس بمادة",
     *     tags={"InstructorSubjects"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف العلاقة بين المدرس والمادة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="subject_id", type="integer", example=2),
     *             @OA\Property(property="instructor_id", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تعديل العلاقة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تعديل العلاقة بين المدرس والمادة بنجاح"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="subject_id", type="integer", example=2),
     *                 @OA\Property(property="instructor_id", type="integer", example=3),
     *                 @OA\Property(property="is_active", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="العلاقة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="العلاقة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function updateInstructorSubject(AssignInstructorToSubjectRequest $request, $id)
    {
        $relation = InstructorSubject::find($id);

        if (!$relation) {
            return $this->error('العلاقة غير موجودة', 404);
        }

        $relation->update($request->validated());

        return $this->successResponse(
            $relation,
            'تم تعديل العلاقة بين المدرس والمادة بنجاح',
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/subjects/delete-teacher-subject/{id}",
     *     summary="حذف علاقة مدرس بمادة",
     *     tags={"InstructorSubjects"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف العلاقة بين المدرس والمادة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف العلاقة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف العلاقة بين المدرس والمادة بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="العلاقة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="العلاقة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function deleteInstructorSubject($id)
    {
        $relation = InstructorSubject::find($id);

        if (!$relation) {
            return $this->error('العلاقة غير موجودة', 404);
        }

        $relation->delete();

        return $this->successResponse(
            null,
            'تم حذف العلاقة بين المدرس والمادة بنجاح',
            200
        );
    }
    /**
     * @OA\Delete(
     *     path="/api/subjects/delete-teacher-subject-by-ids",
     *     summary="حذف علاقة مدرس بمادة باستخدام معرف المدرس ومعرف المادة",
     *     tags={"InstructorSubjects"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"instructor_id","subject_id"},
     *             @OA\Property(
     *                 property="instructor_id",
     *                 type="integer",
     *                 example=5,
     *                 description="معرف المدرس"
     *             ),
     *             @OA\Property(
     *                 property="subject_id",
     *                 type="integer",
     *                 example=12,
     *                 description="معرف المادة"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف العلاقة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف العلاقة بين المدرس والمادة بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="العلاقة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="العلاقة بين المدرس والمادة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function deleteInstructorSubjectByIds(Request $request)
    {
        $request->validate([
            'instructor_id' => 'required|integer|exists:instructors,id',

            'subject_id'    => 'required|integer|exists:subjects,id',
        ]);

        $relation = InstructorSubject::where('instructor_id', $request->instructor_id)
            ->where('subject_id', $request->subject_id)
            ->first();

        if (!$relation) {
            return $this->error(
                'العلاقة بين المدرس والمادة غير موجودة',
                404
            );
        }

        $relation->delete();

        return $this->successResponse(
            null,
            'تم حذف العلاقة بين المدرس والمادة بنجاح',
            200
        );
    }
}
