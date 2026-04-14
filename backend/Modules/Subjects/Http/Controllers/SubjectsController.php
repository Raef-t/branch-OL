<?php

namespace Modules\Subjects\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Subjects\Models\Subject;
use Modules\Subjects\Http\Requests\StoreSubjectRequest;
use Modules\Subjects\Http\Requests\UpdateSubjectRequest;
use Modules\Subjects\Http\Resources\SubjectResource;
use Modules\Shared\Traits\SuccessResponseTrait;

class SubjectsController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/subjects",
     *     summary="قائمة جميع المواد",
     *     tags={"Subjects"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع المواد بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع المواد بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Subject")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد مواد",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي مادة مسجلة حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index()
    {
        // ترتيب المواد من الأحدث إلى الأقدم
        $subjects = Subject::with('academicBranch')
            ->orderBy('id', 'desc') // أو 'created_at' إذا تحب حسب وقت الإضافة
            ->get();

        if ($subjects->isEmpty()) {
            return $this->error('لا يوجد أي مادة مسجلة حالياً', 404);
        }

        return $this->successResponse(
            SubjectResource::collection($subjects),
            'تم جلب جميع المواد بنجاح',
            200
        );
    }


    /**
     * @OA\Post(
     *     path="/api/subjects",
     *     summary="إضافة مادة جديدة",
     *     tags={"Subjects"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","academic_branch_id"},
     *             @OA\Property(property="name", type="string", example="Mathematics"),
     *             @OA\Property(property="description", type="string", example="Introduction to Mathematics"),
     *             @OA\Property(property="academic_branch_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء المادة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء المادة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="academic_branch_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Mathematics"),
     *                 @OA\Property(property="description", type="string", example="Introduction to Mathematics"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     )
     * )
     */
   public function store(StoreSubjectRequest $request)
{
    $subject = Subject::create($request->validated());
    $subject->load('academicBranch'); // ✅ مهم

    return $this->successResponse(
        new SubjectResource($subject),
        'تم إنشاء المادة بنجاح',
        201
    );
}


    /**
     * @OA\Get(
     *     path="/api/subjects/{id}",
     *     summary="عرض تفاصيل مادة محددة",
     *     tags={"Subjects"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف المادة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات المادة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات المادة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="academic_branch_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Mathematics"),
     *                 @OA\Property(property="description", type="string", example="Introduction to Mathematics"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="المادة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="المادة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
   public function show($id)
{
    $subject = Subject::with('academicBranch')->find($id);

    if (!$subject) {
        return $this->error('المادة غير موجودة', 404);
    }

    return $this->successResponse(
        new SubjectResource($subject),
        'تم جلب بيانات المادة بنجاح',
        200
    );
}


    /**
     * @OA\Put(
     *     path="/api/subjects/{id}",
     *     summary="تحديث بيانات مادة",
     *     tags={"Subjects"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف المادة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Mathematics"),
     *             @OA\Property(property="description", type="string", example="Updated Introduction to Mathematics"),
     *             @OA\Property(property="academic_branch_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات المادة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات المادة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="academic_branch_id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="Updated Mathematics"),
     *                 @OA\Property(property="description", type="string", example="Updated Introduction to Mathematics"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-02T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="المادة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="المادة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
public function update(UpdateSubjectRequest $request, $id)
{
    $subject = Subject::find($id);

    if (!$subject) {
        return $this->error('المادة غير موجودة', 404);
    }

    $subject->update($request->validated());
    $subject->load('academicBranch'); // ✅ مهم

    return $this->successResponse(
        new SubjectResource($subject),
        'تم تحديث بيانات المادة بنجاح',
        200
    );
}


    /**
     * @OA\Delete(
     *     path="/api/subjects/{id}",
     *     summary="حذف مادة",
     *     tags={"Subjects"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف المادة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف المادة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف المادة بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="المادة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="المادة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
public function destroy($id)
{
    $subject = Subject::find($id);

    if (!$subject) {
        return $this->error('المادة غير موجودة', 404);
    }

    $subject->delete();

    return $this->successResponse(
        null,
        'تم حذف المادة بنجاح',
        200
    );
}

}