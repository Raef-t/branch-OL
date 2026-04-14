<?php

namespace Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\Users\Http\Requests\LoginRequest;
use Modules\Users\Models\User;
use Modules\Users\Http\Resources\UserResource;
use OpenApi\Annotations as OA;
use Throwable;

class AuthController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="تسجيل الدخول إلى النظام",
     *     description="يُستخدم لتسجيل الدخول باستخدام المعرف الفريد (unique_id) وكلمة المرور. 
     *                  في حال تم إرسال fcm_token، سيتم حفظه أو تحديثه في جدول fcm_tokens لتتبع إشعارات المستخدم على الأجهزة المختلفة.",
     *     operationId="loginUser",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"unique_id", "password"},
     *             @OA\Property(
     *                 property="unique_id",
     *                 type="string",
     *                 example="OAD-00001",
     *                 description="المعرف الفريد للمستخدم (قد يكون خاص بالموظف أو الطالب أو المدير)."
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 example="password123",
     *                 description="كلمة المرور الخاصة بالمستخدم."
     *             ),
     *             @OA\Property(
     *                 property="fcm_token",
     *                 type="string",
     *                 nullable=true,
     *                 example="fcm_1234567890abcdef",
     *                 description="رمز FCM الخاص بالجهاز لتفعيل الإشعارات الفورية (اختياري)."
     *             ),
     *             @OA\Property(
     *                 property="device_info",
     *                 type="object",
     *                 nullable=true,
     *                 example={"model": "Samsung A51", "os": "Android 14", "app_version": "1.0.3"},
     *                 description="معلومات الجهاز مثل نوعه وإصدار النظام (اختياري)."
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم تسجيل الدخول بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تسجيل الدخول بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     description="بيانات المستخدم بعد تسجيل الدخول",
     *                     @OA\Property(property="id", type="integer", example=15),
     *                     @OA\Property(property="unique_id", type="string", example="OAD-00001"),
     *                     @OA\Property(property="name", type="string", example="محمد الأحمد"),
     *                     @OA\Property(property="email", type="string", example="m.ahmad@example.com"),
     *                     @OA\Property(property="type", type="string", example="employee"),
     *                     @OA\Property(property="photo_url", type="string", example="https://example.com/storage/photos/user15.jpg"),
     *                     @OA\Property(
     *                         property="branch",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=3),
     *                         @OA\Property(property="name", type="string", example="فرع جدة")
     *                     ),
     *                     @OA\Property(
     *                         property="roles",
     *                         type="array",
     *                         @OA\Items(type="string", example="admin")
     *                     ),
     *                     @OA\Property(
     *                         property="permissions",
     *                         type="array",
     *                         @OA\Items(type="string", example="manage_users")
     *                     ),
     *                     @OA\Property(
     *                         property="extra",
     *                         type="object",
     *                         description="بيانات إضافية تختلف حسب نوع المستخدم (مثل بيانات الطالب أو الموظف).",
     *                         example={"position": "مشرف", "department": "الشؤون الإدارية"}
     *                     ),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-11 09:15:00")
     *                 ),
     *                 @OA\Property(
     *                     property="token",
     *                     type="string",
     *                     example="1|dfkjgkljdfgkldfghsfdgkjsdfgksjdgksjdg",
     *                     description="رمز الدخول (Bearer Token) المستخدم للمصادقة في الطلبات اللاحقة."
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="بيانات الاعتماد غير صحيحة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="بيانات الاعتماد غير صحيحة")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="خطأ في التحقق من صحة البيانات (validation error)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="بيانات الاعتماد غير صحيحة"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={"unique_id": {"المعرف الفريد مطلوب"}, "password": {"كلمة المرور مطلوبة"}}
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="خطأ في النظام",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="حدث خطأ غير متوقع. يرجى المحاولة لاحقاً.")
     *         )
     *     )
     * )
     */


    public function login(LoginRequest $request)
    {
        try {
            $user = User::with([
                'employee.instituteBranch',
                'student.instituteBranch',
                'student.latestBatchStudent.batch', // المسار الصحيح للعلاقة
                'instructor.instituteBranch',
                'family.students.user',
                'roles',
                'permissions',
            ])->where('unique_id', $request->unique_id)->first();


            if ($user?->employee) {
                Log::info('AUTH DEBUG - Employee fields', [
                    'employee_id' => $user->employee->id,
                    'institute_branch_id' => $user->employee->institute_branch_id,
                ]);

                Log::info('AUTH DEBUG - Employee instituteBranch', [
                    'branch' => $user->employee->instituteBranch,
                ]);
            }

            // تحقق من بيانات الدخول
            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->error('بيانات الاعتماد غير صحيحة', 401);
            }

            // تحقق من حالة الحساب (approval status)
            Log::info('AUTH DEBUG - Checking user approval status', [
                'user_id' => $user->id,
                'unique_id' => $user->unique_id,
                'is_approved_value' => $user->is_approved,
            ]);

            if (!$user->is_approved) {
                return $this->error('حسابك غير مفعل حالياً. يرجى مراجعة إدارة المعهد.', 403);
            }

            // إنشاء التوكن
            $token = $user->createToken('auth-token')->plainTextToken;

            // حفظ FCM Token
            if ($request->filled('fcm_token')) {
                \Modules\FcmTokens\Models\FcmToken::updateOrCreate(
                    [
                        'token' => $request->fcm_token,
                        'user_id' => $user->id,
                    ],
                    [
                        'device_info' => $request->input('device_info', []),
                        'last_seen' => now(),
                    ]
                );
            }

            if ($user->force_password_change && ($user->hasRole('student') || $user->hasRole('family') || $user->hasRole('parent'))) {
                return $this->successResponse([
                    'user' => new UserResource($user),
                    'token' => $token,
                    'must_change_password' => true,
                    'redirect_to' => url('/api/users/change-password'),
                ], 'يجب تغيير كلمة المرور قبل المتابعة.');
            }

            return $this->successResponse([
                'user' => new UserResource($user),
                'token' => $token,
            ], 'تم تسجيل الدخول بنجاح');
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->error('حدث خطأ في قاعدة البيانات أثناء محاولة تسجيل الدخول.', 500);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="تسجيل الخروج",
     *     tags={"Auth"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم تسجيل الخروج بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تسجيل الخروج بنجاح"),
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
    public function logout(Request $request)
    {
        try {
            // حذف Token الحالي
            $request->user()->currentAccessToken()->delete();
            return $this->successResponse(null, 'تم تسجيل الخروج بنجاح');
        } catch (Throwable $e) {
            return $this->error('حدث خطأ أثناء تسجيل الخروج. يرجى المحاولة لاحقاً.', 500);
        }
    }
}
