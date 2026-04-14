<?php

namespace Modules\StudentStatuses\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\StudentStatuses\Http\Requests\StudentStatusesStoreRequest;
use Modules\StudentStatuses\Http\Requests\StudentStatusesUpdateRequest;
use Modules\StudentStatuses\Http\Resources\StudentStatusResource;
use Modules\StudentStatuses\Models\StudentStatus;
use OpenApi\Annotations as OA;

class StudentStatusesController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/student-statuses",
     *     summary="قائمة حالات الطالب",
     *     tags={"Student Statuses"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="نجاح",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/StudentStatusResource")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $statuses = StudentStatus::latest()->get();
        return $this->successResponse(
            StudentStatusResource::collection($statuses),
            'تم جلب حالات الطالب بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/student-statuses",
     *     summary="إنشاء حالة طالب جديدة",
     *     tags={"Student Statuses"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StudentStatusResource")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم الإنشاء بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء حالة الطالب بنجاح"),
     *             @OA\Property(property="data", ref="#/components/schemas/StudentStatusResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="فشل التحقق",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function store(StudentStatusesStoreRequest $request)
    {
        $status = StudentStatus::create($request->validated());

        return $this->successResponse(
            new StudentStatusResource($status),
            'تم إنشاء حالة الطالب بنجاح',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/student-statuses/{id}",
     *     summary="عرض بيانات حالة طالب محددة",
     *     tags={"Student Statuses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الحالة",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="نجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات الحالة بنجاح"),
     *             @OA\Property(property="data", ref="#/components/schemas/StudentStatusResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الحالة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الحالة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $status = StudentStatus::find($id);

        if (!$status) {
            return $this->error('الحالة غير موجودة', 404);
        }

        return $this->successResponse(
            new StudentStatusResource($status),
            'تم جلب بيانات الحالة بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/student-statuses/{id}",
     *     summary="تحديث بيانات حالة طالب",
     *     tags={"Student Statuses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الحالة",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StudentStatusResource")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم التحديث بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات الحالة بنجاح"),
     *             @OA\Property(property="data", ref="#/components/schemas/StudentStatusResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الحالة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الحالة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="فشل التحقق",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function update(StudentStatusesUpdateRequest $request, $id)
    {
        $status = StudentStatus::find($id);

        if (!$status) {
            return $this->error('الحالة غير موجودة', 404);
        }

        $status->update($request->validated());

        return $this->successResponse(
            new StudentStatusResource($status),
            'تم تحديث بيانات الحالة بنجاح',
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/student-statuses/{id}",
     *     summary="حذف حالة طالب",
     *     tags={"Student Statuses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الحالة",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم الحذف بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف الحالة بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الحالة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الحالة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="لا يمكن الحذف لأن الحالة مرتبطة بطلاب",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يمكن حذف الحالة لأنها مرتبطة بطلاب"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $status = StudentStatus::find($id);

        if (!$status) {
            return $this->error('الحالة غير موجودة', 404);
        }

        // منع الحذف إذا كانت الحالة مرتبطة بطلاب
        if ($status->students()->count() > 0) {
            return $this->error('لا يمكن حذف الحالة لأنها مرتبطة بطلاب', 400);
        }

        $status->delete();

        return $this->successResponse(
            null,
            'تم حذف الحالة بنجاح',
            200
        );
    }
}