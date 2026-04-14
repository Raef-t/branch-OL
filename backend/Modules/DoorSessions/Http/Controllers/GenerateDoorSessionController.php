<?php

namespace Modules\DoorSessions\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\DoorDevices\Models\DoorDevice;
use Modules\DoorSessions\Http\Requests\GenerateSessionRequest;
use Modules\DoorSessions\Services\GenerateDoorSessionService;
use Illuminate\Http\JsonResponse;

class GenerateDoorSessionController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/door-sessions/generate",
     *     summary="توليد جلسة جديدة (QR) لجهاز الباب",
     *     description="تقوم أجهزة الأبواب باستدعاء هذا المسار لتوليد جلسة جديدة تحتوي على رمز QR يتم عرضه للطلاب للمسح.  
     *     يجب إرسال **X-DEVICE-ID** و **X-DEVICE-KEY** في الترويسات (Headers) للمصادقة على الجهاز.",
     *     tags={"Door Sessions"},
     *     security={{"deviceAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="بيانات الجهاز المطلوب توليد جلسة له",
     *         @OA\JsonContent(
     *             required={"device_id"},
     *             @OA\Property(
     *                 property="device_id",
     *                 type="string",
     *                 example="DOOR_MAIN_01",
     *                 description="المعرّف الفريد لجهاز الباب (Door Device ID)"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم توليد الجلسة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم توليد الجلسة بنجاح."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="token", type="string", example="7f9c2b4b1a8e2b4b2d9f0c1a5e2b7f9c"),
     *                 @OA\Property(property="expires_at", type="string", format="date-time", example="2025-11-08T09:45:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="فشل في المصادقة - الترويسات غير صحيحة أو الجهاز غير مفعل",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="المفتاح المرسل غير صحيح."),
     *             @OA\Property(property="data", type="null", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لم يتم العثور على الجهاز",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لم يتم العثور على الجهاز المطلوب."),
     *             @OA\Property(property="data", type="null", nullable=true)
     *         )
     *     )
     * )
     *
     * @OA\SecurityScheme(
     *     securityScheme="deviceAuth",
     *     type="apiKey",
     *     in="header",
     *     name="X-DEVICE-KEY",
     *     description="يجب إرسال القيمتين التاليتين في الترويسات:\n
     *     - **X-DEVICE-ID**: معرّف الجهاز (مثل: DOOR_MAIN_01)\n
     *     - **X-DEVICE-KEY**: المفتاح الخاص بالجهاز للتحقق من الصلاحية."
     * )
     */
    public function __invoke(GenerateSessionRequest $request): JsonResponse
    {
        // ✅ تم التحقق من الترويسات عبر الميدل‌وير EnsureDoorDeviceAuthorized

        // جلب الجهاز من المعرّف
        $device = DoorDevice::where('device_id', $request->input('device_id'))->firstOrFail();

        // توليد الجلسة عبر السيرفيس
        $session = app(GenerateDoorSessionService::class)->createForDevice($device);

        return response()->json([
            'status' => true,
            'message' => 'تم توليد الجلسة بنجاح.',
            'data' => [
                'token' => $session->session_token,
                'expires_at' => $session->expires_at->toISOString(),
            ],
        ], 201);
    }
}
