<?php
namespace Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Users\Services\UserApprovalService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserApprovalController extends Controller
{
    /**
 * @OA\Post(
 *     path="/api/users/{id}/approve",
 *     summary="اعتماد مستخدم من قبل مسؤول",
 *     description="يسمح للمستخدمين ذوي الدور 'admin' باعتماد مستخدم آخر (تفعيل حسابه). يتطلب توكن مصادقة صالح.",
 *     operationId="approveUser",
 *     tags={"Users"},
 *     security={{"sanctum":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="معرف المستخدم المراد اعتماده",
 *         @OA\Schema(type="integer", example=12)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="تم اعتماد المستخدم بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="تمت الموافقة على المستخدم بنجاح."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=12),
 *                 @OA\Property(property="name", type="string", example="خالد أحمد"),
 *                 @OA\Property(property="email", type="string", example="khalid@example.com"),
 *                 @OA\Property(property="role", type="string", example="student"),
 *                 @OA\Property(property="is_approved", type="boolean", example=true),
 *                 @OA\Property(property="approved_by", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="غير مصرح (المستخدم غير مصادق عليه)",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="غير مصرح (المستخدم ليس مسؤولًا)",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="This action is unauthorized.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="المستخدم المطلوب غير موجود",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="User not found.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="فشل منطقي (مثل: المستخدم معتمد مسبقًا)",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="المستخدم معتمد بالفعل.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="خطأ داخلي في الخادم",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="حدث خطأ أثناء الموافقة على المستخدم.")
 *         )
 *     )
 * )
 */
    public function approve($id, UserApprovalService $service)
    {
        try {
            $user = $service->approve($id);

            return $this->successResponse($user, 'تمت الموافقة على المستخدم بنجاح.');
        } catch (\DomainException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->error('حدث خطأ أثناء الموافقة على المستخدم.', 500);
        }
    }

    /**
     * Return a standard JSON success response.
     *
     * @param mixed  $data
     * @param string $message
     * @param int    $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data, $message = '', $status = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Return a standard JSON error response.
     *
     * @param string $message
     * @param int    $status
     * @param mixed  $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error($message = '', $status = 400, $errors = null)
    {
        $payload = [
            'status' => 'error',
            'message' => $message,
        ];

        if (!is_null($errors)) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }
}
