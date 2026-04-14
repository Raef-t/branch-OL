<?php

namespace Modules\DoorDevices\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\DoorDevices\Models\DoorDevice;
use Modules\DoorDevices\Http\Requests\StoreDoorDeviceRequest;
use Modules\DoorDevices\Http\Requests\UpdateDoorDeviceRequest;
use Modules\DoorDevices\Http\Resources\DoorDeviceResource;
use Modules\Shared\Traits\SuccessResponseTrait;

class DoorDevicesController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/door-devices",
     *     summary="قائمة جميع أجهزة الأبواب",
     *     tags={"DoorDevices"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع أجهزة الأبواب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع أجهزة الأبواب بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="device_id", type="string", example="DOOR_MAIN_01"),
     *                     @OA\Property(property="name", type="string", example="جهاز الباب الرئيسي"),
     *                     @OA\Property(property="location", type="string", example="المدخل الشمالي", nullable=true),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="last_seen_at", type="string", format="date-time", example="2025-09-29T11:58:00Z", nullable=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T11:58:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-29T11:58:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد أجهزة أبواب",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي جهاز أبواب مسجل حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $doorDevices = DoorDevice::all();

        if ($doorDevices->isEmpty()) {
            return $this->error('لا يوجد أي جهاز أبواب مسجل حالياً', 404);
        }

        return $this->successResponse(
            DoorDeviceResource::collection($doorDevices),
            'تم جلب جميع أجهزة الأبواب بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/door-devices",
     *     summary="إضافة جهاز باب جديد",
     *     tags={"DoorDevices"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"device_id","name"},
     *             @OA\Property(property="device_id", type="string", example="DOOR_MAIN_01"),
     *             @OA\Property(property="name", type="string", example="جهاز الباب الرئيسي"),
     *             @OA\Property(property="location", type="string", example="المدخل الشمالي", nullable=true),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *             @OA\Property(property="last_seen_at", type="string", format="date-time", example="2025-09-29T11:58:00Z", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء جهاز الباب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء جهاز الباب بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="device_id", type="string", example="DOOR_MAIN_01"),
     *                 @OA\Property(property="name", type="string", example="جهاز الباب الرئيسي"),
     *                 @OA\Property(property="location", type="string", example="المدخل الشمالي", nullable=true),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="last_seen_at", type="string", format="date-time", example="2025-09-29T11:58:00Z", nullable=true),
     *   @OA\Property(property="api_key", type="string", example="abcdef123456..."),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T11:58:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-29T11:58:00Z")
     *             )
     *         )
     *     )
     * )
     */ public function store(StoreDoorDeviceRequest $request)
    {
        $data = $request->validated();

        $data['api_key'] = bin2hex(random_bytes(32)); // مفتاح بطول 64 حرف (آمن وفريد)

        $doorDevice = DoorDevice::create($data);

        return $this->successResponse(
            new DoorDeviceResource($doorDevice),
            'تم إنشاء جهاز الباب بنجاح',
            201
        );
    }


    /**
     * @OA\Get(
     *     path="/api/door-devices/{id}",
     *     summary="عرض تفاصيل جهاز باب محدد",
     *     tags={"DoorDevices"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف جهاز الباب",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات جهاز الباب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات جهاز الباب بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="device_id", type="string", example="DOOR_MAIN_01"),
     *                 @OA\Property(property="name", type="string", example="جهاز الباب الرئيسي"),
     *                 @OA\Property(property="location", type="string", example="المدخل الشمالي", nullable=true),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="last_seen_at", type="string", format="date-time", example="2025-09-29T11:58:00Z", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T11:58:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-29T11:58:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="جهاز الباب غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="جهاز الباب غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $doorDevice = DoorDevice::find($id);

        if (!$doorDevice) {
            return $this->error('جهاز الباب غير موجود', 404);
        }

        return $this->successResponse(
            new DoorDeviceResource($doorDevice),
            'تم جلب بيانات جهاز الباب بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/door-devices/{id}",
     *     summary="تحديث بيانات جهاز باب",
     *     tags={"DoorDevices"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف جهاز الباب",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="device_id", type="string", example="DOOR_MAIN_02"),
     *             @OA\Property(property="name", type="string", example="جهاز الباب الثانوي"),
     *             @OA\Property(property="location", type="string", example="المدخل الجنوبي", nullable=true),
     *             @OA\Property(property="is_active", type="boolean", example=false),
     *             @OA\Property(property="last_seen_at", type="string", format="date-time", example="2025-09-29T12:58:00Z", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات جهاز الباب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات جهاز الباب بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="device_id", type="string", example="DOOR_MAIN_02"),
     *                 @OA\Property(property="name", type="string", example="جهاز الباب الثانوي"),
     *                 @OA\Property(property="location", type="string", example="المدخل الجنوبي", nullable=true),
     *                 @OA\Property(property="is_active", type="boolean", example=false),
     *                 @OA\Property(property="last_seen_at", type="string", format="date-time", example="2025-09-29T12:58:00Z", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T11:58:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-29T12:58:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="جهاز الباب غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="جهاز الباب غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function update(UpdateDoorDeviceRequest $request, $id)
    {
        $doorDevice = DoorDevice::find($id);

        if (!$doorDevice) {
            return $this->error('جهاز الباب غير موجود', 404);
        }

        $doorDevice->update($request->validated());

        return $this->successResponse(
            new DoorDeviceResource($doorDevice),
            'تم تحديث بيانات جهاز الباب بنجاح',
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/door-devices/{id}",
     *     summary="حذف جهاز باب",
     *     tags={"DoorDevices"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف جهاز الباب",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف جهاز الباب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف جهاز الباب بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="جهاز الباب غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="جهاز الباب غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $doorDevice = DoorDevice::find($id);

        if (!$doorDevice) {
            return $this->error('جهاز الباب غير موجود', 404);
        }

        $doorDevice->delete();

        return $this->successResponse(
            null,
            'تم حذف جهاز الباب بنجاح',
            200
        );
    }
}
