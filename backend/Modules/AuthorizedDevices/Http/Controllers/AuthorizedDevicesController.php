<?php

namespace Modules\AuthorizedDevices\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\AuthorizedDevices\Models\AuthorizedDevice;
use Modules\AuthorizedDevices\Http\Requests\StoreAuthorizedDeviceRequest;
use Modules\AuthorizedDevices\Http\Requests\UpdateAuthorizedDeviceRequest;
use Modules\AuthorizedDevices\Http\Resources\AuthorizedDeviceResource;
use Modules\Shared\Traits\SuccessResponseTrait;

class AuthorizedDevicesController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/authorized-devices",
     *     summary="قائمة جميع الأجهزة المصرح لها",
     *     tags={"AuthorizedDevices"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع الأجهزة المصرح لها بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع الأجهزة المصرح لها بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="device_id", type="string", example="DEVICE-123456"),
     *                     @OA\Property(property="device_name", type="string", example="جهاز الباب الرئيسي", nullable=true),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="last_used_at", type="string", format="date-time", example="2025-09-29T12:26:00Z", nullable=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T12:26:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-29T12:26:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد أجهزة مصرح لها",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي أجهزة مصرح لها مسجلة حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $authorizedDevices = AuthorizedDevice::all();

        if ($authorizedDevices->isEmpty()) {
            return $this->error('لا يوجد أي أجهزة مصرح لها مسجلة حالياً', 404);
        }

        return $this->successResponse(
            AuthorizedDeviceResource::collection($authorizedDevices),
            'تم جلب جميع الأجهزة المصرح لها بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/authorized-devices",
     *     summary="إضافة جهاز مصرح جديد",
     *     tags={"AuthorizedDevices"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"device_id"},
     *             @OA\Property(property="device_id", type="string", example="DEVICE-123456"),
     *             @OA\Property(property="device_name", type="string", example="جهاز الباب الرئيسي", nullable=true),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *             @OA\Property(property="last_used_at", type="string", format="date-time", example="2025-09-29T12:26:00Z", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء الجهاز المصرح بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء الجهاز المصرح بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="device_id", type="string", example="DEVICE-123456"),
     *                 @OA\Property(property="device_name", type="string", example="جهاز الباب الرئيسي", nullable=true),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="last_used_at", type="string", format="date-time", example="2025-09-29T12:26:00Z", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T12:26:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-29T12:26:00Z")
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreAuthorizedDeviceRequest $request)
    {
        $authorizedDevice = AuthorizedDevice::create($request->validated());

        return $this->successResponse(
            new AuthorizedDeviceResource($authorizedDevice),
            'تم إنشاء الجهاز المصرح بنجاح',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/authorized-devices/{id}",
     *     summary="عرض تفاصيل جهاز مصرح محدد",
     *     tags={"AuthorizedDevices"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الجهاز المصرح",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات الجهاز المصرح بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات الجهاز المصرح بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="device_id", type="string", example="DEVICE-123456"),
     *                 @OA\Property(property="device_name", type="string", example="جهاز الباب الرئيسي", nullable=true),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="last_used_at", type="string", format="date-time", example="2025-09-29T12:26:00Z", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T12:26:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-29T12:26:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الجهاز المصرح غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الجهاز المصرح غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $authorizedDevice = AuthorizedDevice::find($id);

        if (!$authorizedDevice) {
            return $this->error('الجهاز المصرح غير موجود', 404);
        }

        return $this->successResponse(
            new AuthorizedDeviceResource($authorizedDevice),
            'تم جلب بيانات الجهاز المصرح بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/authorized-devices/{id}",
     *     summary="تحديث بيانات جهاز مصرح",
     *     tags={"AuthorizedDevices"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الجهاز المصرح",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="device_id", type="string", example="DEVICE-654321"),
     *             @OA\Property(property="device_name", type="string", example="جهاز الباب الثانوي", nullable=true),
     *             @OA\Property(property="is_active", type="boolean", example=false),
     *             @OA\Property(property="last_used_at", type="string", format="date-time", example="2025-09-29T12:30:00Z", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات الجهاز المصرح بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات الجهاز المصرح بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="device_id", type="string", example="DEVICE-654321"),
     *                 @OA\Property(property="device_name", type="string", example="جهاز الباب الثانوي", nullable=true),
     *                 @OA\Property(property="is_active", type="boolean", example=false),
     *                 @OA\Property(property="last_used_at", type="string", format="date-time", example="2025-09-29T12:30:00Z", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T12:26:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-29T12:30:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الجهاز المصرح غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الجهاز المصرح غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function update(UpdateAuthorizedDeviceRequest $request, $id)
    {
        $authorizedDevice = AuthorizedDevice::find($id);

        if (!$authorizedDevice) {
            return $this->error('الجهاز المصرح غير موجود', 404);
        }

        $authorizedDevice->update($request->validated());

        return $this->successResponse(
            new AuthorizedDeviceResource($authorizedDevice),
            'تم تحديث بيانات الجهاز المصرح بنجاح',
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/authorized-devices/{id}",
     *     summary="حذف جهاز مصرح",
     *     tags={"AuthorizedDevices"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الجهاز المصرح",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف الجهاز المصرح بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف الجهاز المصرح بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الجهاز المصرح غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الجهاز المصرح غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $authorizedDevice = AuthorizedDevice::find($id);

        if (!$authorizedDevice) {
            return $this->error('الجهاز المصرح غير موجود', 404);
        }

        $authorizedDevice->delete();

        return $this->successResponse(
            null,
            'تم حذف الجهاز المصرح بنجاح',
            200
        );
    }
}