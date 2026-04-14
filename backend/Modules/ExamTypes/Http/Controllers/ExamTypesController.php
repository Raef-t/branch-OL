<?php

namespace Modules\ExamTypes\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\ExamTypes\Models\ExamType;
use Modules\ExamTypes\Http\Requests\StoreExamTypeRequest;
use Modules\ExamTypes\Http\Requests\UpdateExamTypeRequest;
use Modules\ExamTypes\Http\Resources\ExamTypeResource;
use Modules\Shared\Traits\SuccessResponseTrait;

class ExamTypesController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/exam-types",
     *     summary="قائمة جميع أنواع الامتحانات",
     *     tags={"Exam Types"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع أنواع الامتحانات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع أنواع الامتحانات بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="midterm"),
     *                     @OA\Property(property="description", type="string", example="امتحان منتصف الفصل"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد أنواع امتحانات",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي نوع امتحان مسجل حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $examTypes = ExamType::all();

        if ($examTypes->isEmpty()) {
            return $this->error('لا يوجد أي نوع امتحان مسجل حالياً', 404);
        }

        return $this->successResponse(
            ExamTypeResource::collection($examTypes),
            'تم جلب جميع أنواع الامتحانات بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/exam-types",
     *     summary="إضافة نوع امتحان جديد",
     *     tags={"Exam Types"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="midterm"),
     *             @OA\Property(property="description", type="string", example="امتحان منتصف الفصل")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء نوع الامتحان بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء نوع الامتحان بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="midterm"),
     *                 @OA\Property(property="description", type="string", example="امتحان منتصف الفصل"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreExamTypeRequest $request)
    {
        $examType = ExamType::create($request->validated());

        return $this->successResponse(
            new ExamTypeResource($examType),
            'تم إنشاء نوع الامتحان بنجاح',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/exam-types/{id}",
     *     summary="عرض تفاصيل نوع امتحان محدد",
     *     tags={"Exam Types"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف نوع الامتحان",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات نوع الامتحان بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات نوع الامتحان بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="midterm"),
     *                 @OA\Property(property="description", type="string", example="امتحان منتصف الفصل"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="نوع الامتحان غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="نوع الامتحان غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $examType = ExamType::find($id);

        if (!$examType) {
            return $this->error('نوع الامتحان غير موجود', 404);
        }

        return $this->successResponse(
            new ExamTypeResource($examType),
            'تم جلب بيانات نوع الامتحان بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/exam-types/{id}",
     *     summary="تحديث بيانات نوع امتحان",
     *     tags={"Exam Types"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف نوع الامتحان",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="final"),
     *             @OA\Property(property="description", type="string", example="امتحان نهائي شامل")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات نوع الامتحان بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات نوع الامتحان بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="final"),
     *                 @OA\Property(property="description", type="string", example="امتحان نهائي شامل"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-02-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="نوع الامتحان غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="نوع الامتحان غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function update(UpdateExamTypeRequest $request, $id)
    {
        $examType = ExamType::find($id);

        if (!$examType) {
            return $this->error('نوع الامتحان غير موجود', 404);
        }

        $examType->update($request->validated());

        return $this->successResponse(
            new ExamTypeResource($examType),
            'تم تحديث بيانات نوع الامتحان بنجاح',
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/exam-types/{id}",
     *     summary="حذف نوع امتحان",
     *     tags={"Exam Types"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف نوع الامتحان",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف نوع الامتحان بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف نوع الامتحان بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="نوع الامتحان غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="نوع الامتحان غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $examType = ExamType::find($id);

        if (!$examType) {
            return $this->error('نوع الامتحان غير موجود', 404);
        }

        $examType->delete();

        return $this->successResponse(
            null,
            'تم حذف نوع الامتحان بنجاح',
            200
        );
    }
}