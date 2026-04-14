<?php

namespace Modules\AcademicRecords\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\AcademicRecords\Http\Requests\AcademicRecordsStoreRequest;
use Modules\AcademicRecords\Http\Requests\AcademicRecordsUpdateRequest;
use Modules\AcademicRecords\Http\Resources\AcademicRecordResource;
use Modules\AcademicRecords\Models\AcademicRecord;
use OpenApi\Annotations as OA;

class AcademicRecordsController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/academic-records",
     *     summary="قائمة السجلات الأكاديمية",
     *     tags={"Academic Records"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="نجاح",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/AcademicRecordResource")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $records = AcademicRecord::with('student')->latest()->get();
        return $this->successResponse(
            AcademicRecordResource::collection($records),
            'تم جلب السجلات الأكاديمية بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/academic-records",
     *     summary="إنشاء سجل أكاديمي جديد",
     *     tags={"Academic Records"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AcademicRecordResource")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم الإنشاء بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء السجل الأكاديمي بنجاح"),
     *             @OA\Property(property="data", ref="#/components/schemas/AcademicRecordResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="فشل التحقق",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function store(AcademicRecordsStoreRequest $request)
    {
        $record = AcademicRecord::create($request->validated());

        $record->load('student');

        return $this->successResponse(
            new AcademicRecordResource($record),
            'تم إنشاء السجل الأكاديمي بنجاح',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/academic-records/{id}",
     *     summary="عرض بيانات سجل أكاديمي محدد",
     *     tags={"Academic Records"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف السجل",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="نجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات السجل بنجاح"),
     *             @OA\Property(property="data", ref="#/components/schemas/AcademicRecordResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="السجل غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="السجل غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $record = AcademicRecord::with('student')->find($id);

        if (!$record) {
            return $this->error('السجل غير موجود', 404);
        }

        return $this->successResponse(
            new AcademicRecordResource($record),
            'تم جلب بيانات السجل بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/academic-records/{id}",
     *     summary="تحديث بيانات سجل أكاديمي",
     *     tags={"Academic Records"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف السجل",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AcademicRecordResource")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم التحديث بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات السجل بنجاح"),
     *             @OA\Property(property="data", ref="#/components/schemas/AcademicRecordResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="السجل غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="السجل غير موجود"),
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
    public function update(AcademicRecordsUpdateRequest $request, $id)
    {
        $record = AcademicRecord::find($id);

        if (!$record) {
            return $this->error('السجل غير موجود', 404);
        }

        $record->update($request->validated());
        $record->load('student');

        return $this->successResponse(
            new AcademicRecordResource($record),
            'تم تحديث بيانات السجل بنجاح',
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/academic-records/{id}",
     *     summary="حذف سجل أكاديمي",
     *     tags={"Academic Records"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف السجل",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم الحذف بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف السجل بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="السجل غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="السجل غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $record = AcademicRecord::find($id);

        if (!$record) {
            return $this->error('السجل غير موجود', 404);
        }

        $record->delete();

        return $this->successResponse(
            null,
            'تم حذف السجل بنجاح',
            200
        );
    }
}