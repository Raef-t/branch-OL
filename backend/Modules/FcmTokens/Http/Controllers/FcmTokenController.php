<?php
namespace Modules\FcmTokens\Http\Controllers;
use App\Http\Controllers\Controller;
use Modules\FcmTokens\Models\FcmToken;
use Modules\FcmTokens\Http\Requests\FcmTokenRequest;
use Modules\FcmTokens\Http\Resources\FcmTokenResource;
use Modules\Shared\Traits\SuccessResponseTrait;

class FcmTokenController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/fcm-tokens",
     *     summary="عرض جميع التوكنات",
     *     tags={"FcmTokens"},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع التوكنات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع التوكنات بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="token", type="string", example="fcm_token_example_123"),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="device_info", type="string", example="iPhone 14"),
     *                     @OA\Property(property="last_seen", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد توكنات",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي توكنات حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $tokens = FcmToken::all();

        if ($tokens->isEmpty()) {
            return $this->error('لا يوجد أي توكنات حالياً', 404);
        }

        return $this->successResponse(
            FcmTokenResource::collection($tokens),
            'تم جلب جميع التوكنات بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/fcm-tokens",
     *     summary="إنشاء أو تحديث FCM Token",
     *     tags={"FcmTokens"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token"},
     *             @OA\Property(property="token", type="string", example="fcm_token_example_123"),
     *             @OA\Property(property="device_info", type="string", example="iPhone 14")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم حفظ التوكن بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حفظ التوكن بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="token", type="string", example="fcm_token_example_123"),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="device_info", type="string", example="iPhone 14"),
     *                 @OA\Property(property="last_seen", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     )
     * )
     */
    public function store(FcmTokenRequest $request)
    {
        $token = FcmToken::updateOrCreate(
            ['token' => $request->token],
            [
                'user_id' => $request->user_id ?? null,
                'device_info' => $request->device_info,
                'last_seen' => now(),
            ]
        );

        return $this->successResponse(
            new FcmTokenResource($token),
            'تم حفظ التوكن بنجاح',
            201
        );
    }

    /**
     * @OA\Put(
     *     path="/api/fcm-tokens/{id}",
     *     summary="تحديث بيانات توكن معين",
     *     tags={"FcmTokens"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف التوكن",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="fcm_token_example_updated"),
     *             @OA\Property(property="device_info", type="string", example="iPhone 15")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات التوكن بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات التوكن بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="token", type="string", example="fcm_token_example_updated"),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="device_info", type="string", example="iPhone 15"),
     *                 @OA\Property(property="last_seen", type="string", format="date-time", example="2023-01-02T00:00:00.000000Z"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-02T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="التوكن غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="التوكن غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function update(FcmTokenRequest $request, $id)
    {
        $token = FcmToken::where('id', $id)->first();

        if (!$token) {
            return $this->error('التوكن غير موجود', 404);
        }

        $token->update($request->validated());

        return $this->successResponse(
            new FcmTokenResource($token),
            'تم تحديث بيانات التوكن بنجاح',
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/fcm-tokens/{id}",
     *     summary="حذف توكن معين",
     *     tags={"FcmTokens"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف التوكن",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف التوكن بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف التوكن بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="التوكن غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="التوكن غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $token = FcmToken::where('id', $id)->first();

        if (!$token) {
            return $this->error('التوكن غير موجود', 404);
        }

        $token->delete();

        return $this->successResponse(null, 'تم حذف التوكن بنجاح', 200);
    }
}