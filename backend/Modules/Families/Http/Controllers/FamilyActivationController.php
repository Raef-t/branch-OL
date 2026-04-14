<?php

namespace Modules\Families\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\Families\Http\Requests\ActivateFamilyUserRequest;
use Modules\Families\Models\Family;
use Modules\Families\Services\CreateFamilyUserService;
use Modules\Users\Http\Resources\UserResource;
use OpenApi\Annotations as OA;

class FamilyActivationController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Post(
     *     path="/api/families/{family}/activate-user",
     *     summary="تفعيل حساب عائلة (إنشاء مستخدم مرتبط)",
     *     description="يقوم بإنشاء مستخدم من نوع 'family' وربطه بالعائلة المحددة. يُولّد معرف فريد بالشكل OFM-XXXXXXX.",
     *     tags={"Families"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="family",
     *         in="path",
     *         required=true,
     *         description="معرف العائلة",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء الحساب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء حساب العائلة بنجاح"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="العائلة غير صالحة (مثل: لا تحتوي على طالب، أو مرتبطة بمستخدم مسبقًا)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="هذه العائلة مرتبطة بحساب مستخدم بالفعل."),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="العائلة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="العائلة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطأ فني (نادر جدًا)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="حدث خطأ فني أثناء إنشاء الحساب. يرجى المحاولة لاحقًا."),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح بالوصول",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function __invoke(ActivateFamilyUserRequest $request, $family)
    {
        try {
            $family = Family::findOrFail($family);
            $service = new CreateFamilyUserService();
            $user = $service->createForFamily($family);

            return $this->successResponse(
                new UserResource($user),
                'تم إنشاء حساب العائلة بنجاح',
                201
            );
        } catch (\DomainException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\RuntimeException $e) {
            return $this->error('حدث خطأ فني أثناء إنشاء الحساب. يرجى المحاولة لاحقًا.', 500);
        }
    }
}