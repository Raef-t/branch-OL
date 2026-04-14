<?php

namespace Modules\Guardians\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Guardians\Http\Resources\GuardianDashboardResource;
use Modules\Shared\Traits\SuccessResponseTrait;

class GuardianDashboardController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/guardians/dashboard",
     *     summary="بيانات لوحة تحكم ولي الأمر للجوال",
     *     description="تجمع هذه الواجهة بيانات ولي الأمر، والملخص المالي لجميع أبنائه، وقائمة الأبناء.",
     *     tags={"Guardians Mobile"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب البيانات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات لوحة التحكم بنجاح"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="العائلة غير مرتبطة بهذا الحساب"
     *     )
     * )
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // جلب العائلة المرتبطة بالمستخدم
        $family = $user->family()->with([
            'students.latestActiveEnrollmentContract',
            'students.latestBatchStudent.batch',
            'guardians'
        ])->first();

        if (!$family) {
            return $this->error('لم يتم العثور على بيانات العائلة المرتبطة بهذا الحساب', 404);
        }

        return $this->successResponse(
            new GuardianDashboardResource($family),
            'تم جلب بيانات لوحة التحكم بنجاح',
            200
        );
    }
}
