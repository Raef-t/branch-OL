<?php

namespace Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Modules\Users\Models\User;
use Modules\Users\Http\Requests\StoreUserRequest;
use Modules\Users\Http\Requests\UpdateUserRequest;
use Modules\Users\Http\Resources\UserResource;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\Users\Http\Requests\ChangePasswordRequest;

class UsersController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="قائمة جميع المستخدمين",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع المستخدمين بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع المستخدمين بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="unique_id", type="string", example="user123"),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="role", type="string", example="admin"),
     *                     @OA\Property(property="is_approved", type="boolean", example=true),
     *                     @OA\Property(property="force_password_change", type="boolean", example=false),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد مستخدمين",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي مستخدم مسجل حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index()
    {
        // تحميل جميع العلاقات المطلوبة دفعة واحدة (Eager Loading)
        $users = User::with([
            'employee.instituteBranch',
            'instructor.instituteBranch',
            'student.instituteBranch',
            'student.batchStudents',
            'family.students.instituteBranch',
            'roles',
            'permissions'
        ])->get();

        if ($users->isEmpty()) {
            return $this->successResponse(
                UserResource::collection([]),
                'لا يوجد أي مستخدم مسجل حالياً',
                200
            );
        }

        return $this->successResponse(
            UserResource::collection($users),
            'تم جلب جميع المستخدمين بنجاح',
            200
        );
    }


    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="إضافة مستخدم جديد",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"unique_id","name","password","role"},
     *             @OA\Property(property="unique_id", type="string", example="user123"),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="role", type="string", example="admin"),
     *             @OA\Property(property="is_approved", type="boolean", example=true),
     *             @OA\Property(property="force_password_change", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء المستخدم بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء المستخدم بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="unique_id", type="string", example="user123"),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="role", type="string", example="admin"),
     *                 @OA\Property(property="is_approved", type="boolean", example=true),
     *                 @OA\Property(property="force_password_change", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());

        // مزامنة الدور مع نظام Spatie
        if ($request->has('role')) {
            $user->assignRole($request->role);
        }

        return $this->successResponse(
            new UserResource($user),
            'تم إنشاء المستخدم بنجاح',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="عرض تفاصيل مستخدم محدد",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف المستخدم",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات المستخدم بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات المستخدم بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="unique_id", type="string", example="user123"),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="role", type="string", example="admin"),
     *                 @OA\Property(property="is_approved", type="boolean", example=true),
     *                 @OA\Property(property="force_password_change", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="المستخدم غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="المستخدم غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */

    public function show($id)
    {
        $user = User::with([
            'employee.instituteBranch',
            'instructor.instituteBranch',
            'student.instituteBranch',
            'student.batches',
            'family.students.instituteBranch',
            'roles',
            'permissions'
        ])->find($id);

        if (! $user) {
            return $this->error('المستخدم غير موجود', 404);
        }

        return $this->successResponse(
            new UserResource($user),
            'تم جلب بيانات المستخدم بنجاح'
        );
    }


    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="تحديث بيانات مستخدم",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف المستخدم",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="unique_id", type="string", example="updated_user123"),
     *             @OA\Property(property="name", type="string", example="Updated John Doe"),
     *             @OA\Property(property="password", type="string", example="newpassword123"),
     *             @OA\Property(property="role", type="string", example="staff"),
     *             @OA\Property(property="is_approved", type="boolean", example=false),
     *             @OA\Property(property="force_password_change", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات المستخدم بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات المستخدم بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="unique_id", type="string", example="updated_user123"),
     *                 @OA\Property(property="name", type="string", example="Updated John Doe"),
     *                 @OA\Property(property="role", type="string", example="staff"),
     *                 @OA\Property(property="is_approved", type="boolean", example=false),
     *                 @OA\Property(property="force_password_change", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-02T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="المستخدم غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="المستخدم غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->error('المستخدم غير موجود', 404);
        }

        $user->update($request->validated());
        
        // إذا تم تحديث كلمة المرور يدوياً من الأدمن، نجبر المستخدم على تغييرها ونطرد الجلسات القديمة
        if ($request->filled('password')) {
            $user->update(['force_password_change' => true]);
            $user->tokens()->delete();
        }

        // إذا تم تحديث الدور المادي، نقوم بمزامنته مع Spatie (استخدام syncRoles هنا مقبول لأنه تحديث شامل)
        if ($request->has('role')) {
            $user->syncRoles([$request->role]);
        }

        return $this->successResponse(
            new UserResource($user),
            'تم تحديث بيانات المستخدم بنجاح',
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="حذف مستخدم",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف المستخدم",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف المستخدم بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف المستخدم بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="المستخدم غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="المستخدم غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->error('المستخدم غير موجود', 404);
        }

        $user->delete();

        return $this->successResponse(
            null,
            'تم حذف المستخدم بنجاح',
            200
        );
    }
    /**
     * @OA\Post(
     *     path="/api/users/change-password",
     *     summary="تغيير كلمة المرور",
     *     description="يسمح للمستخدم بتغيير كلمة المرور الحالية. بعد التغيير، يُعطَل حقل force_password_change ويتم تسجيل الخروج من جميع الجلسات.",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password","new_password","new_password_confirmation"},
     *             @OA\Property(property="current_password", type="string", example="Pass1234"),
     *             @OA\Property(property="new_password", type="string", example="NewPass123!"),
     *             @OA\Property(property="new_password_confirmation", type="string", example="NewPass123!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تغيير كلمة المرور بنجاح وتم تسجيل الخروج من جميع الأجهزة.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تغيير كلمة المرور بنجاح. يرجى تسجيل الدخول مجددًا."),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="بيانات غير صالحة (مثل: كلمة المرور الحالية خاطئة)",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="كلمة المرور الحالية غير صحيحة."),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error('كلمة المرور الحالية غير صحيحة.', 422);
        }

        // تحديث كلمة المرور وتعطيل الإجبار
        $user->update([
            'password' => Hash::make($request->new_password),
            'force_password_change' => false,
        ]);

        // ✅ تسجيل الخروج من جميع الأجهزة (إبطال جميع التوكنات)
        $user->tokens()->delete();

        return $this->successResponse(
            null,
            'تم تغيير كلمة المرور بنجاح. يرجى تسجيل الدخول مجددًا.',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}/roles",
     *     summary="إضافة دور للمستخدم",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف المستخدم",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="role", type="string", example="admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم إضافة الدور بنجاح"
     *     )
     * )
     */
    public function assignRole(\Illuminate\Http\Request $request, $id)
    {
        $request->validate(['role' => 'required|string']);
        
        $user = User::find($id);
        if (!$user) {
            return $this->error('المستخدم غير موجود', 404);
        }

        try {
            // إضافة الدور (Additive)
            $user->assignRole($request->role);
            
            // تحديث الحقل المادي 'role' في جدول users للمزامنة (لأغراض الـ Middleware والواجهات القديمة)
            // إذا كان الدور هو 'admin' نجعل الحقل المادي 'admin'
            // خلاف ذلك، إذا كان الموظف له أدوار أخرى، نترك الحقل المادي كـ 'staff' أو 'employee'
            if ($request->role === 'admin') {
                $user->update(['role' => 'admin']);
            } else if (str_starts_with($request->role, 'employee_') || in_array($request->role, ['manager', 'teacher'])) {
                 // تأكد من عدم الكتابة فوق 'admin' إذا كان موجوداً مسبقاً
                if ($user->role !== 'admin') {
                    $user->update(['role' => 'employee']);
                }
            } else if ($request->role === 'student') {
                $user->update(['role' => 'student']);
            } else if ($request->role === 'parent') {
                $user->update(['role' => 'family']);
            }

            return $this->successResponse(null, 'تم إضافة الدور بنجاح', 200);
        } catch (\Exception $e) {
            return $this->error('الدور غير موجود أو حدث خطأ: ' . $e->getMessage(), 422);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}/roles/{role}",
     *     summary="إزالة دور من المستخدم",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف المستخدم"
     *     ),
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         required=true,
     *         description="اسم الدور لإزالته"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم إزالة الدور بنجاح"
     *     )
     * )
     */
    public function removeRole($id, $role)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->error('المستخدم غير موجود', 404);
        }

        if ($user->hasRole($role)) {
            $user->removeRole($role);
            
            // إذا قمنا بإزالة دور الـ admin، نحدث الحقل المادي ليعكس أقرب دور أو نجعله employee
            if ($role === 'admin') {
                $user->update(['role' => 'employee']);
            }
        }

        return $this->successResponse(null, 'تم إزالة الدور بنجاح', 200);
    }
    /**
     * @OA\Post(
     *     path="/api/users/{id}/toggle-status",
     *     summary="تبديل حالة تفعيل حساب المستخدم",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف المستخدم",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تبديل الحالة بنجاح"
     *     )
     * )
     */
    public function toggleStatus($id, \Illuminate\Http\Request $request)
    {
        \Illuminate\Support\Facades\Log::info('TOGGLE DEBUG - Request received', [
            'user_id' => $id,
            'provided_status' => $request->status
        ]);
        
        $user = User::find($id);
        if (!$user) {
            \Illuminate\Support\Facades\Log::error('TOGGLE DEBUG - User not found', ['user_id' => $id]);
            return $this->error('المستخدم غير موجود', 404);
        }

        $oldStatus = $user->is_approved;
        
        // إذا تم إرسال الحالة صراحة، نستخدمها، وإلا نقوم بالتبديل
        if ($request->has('status')) {
            $user->is_approved = filter_var($request->status, FILTER_VALIDATE_BOOLEAN);
        } else {
            $user->is_approved = !$user->is_approved;
        }
        
        \Illuminate\Support\Facades\Log::info('TOGGLE DEBUG - Updating status', [
            'user_id' => $id,
            'old_status' => $oldStatus,
            'new_status' => $user->is_approved
        ]);
        
        // إذا تم إيقاف الحساب، نحذف جميع التوكنات لطرده من النظام فوراً
        if (!$user->is_approved) {
            $user->tokens()->delete();
            \Illuminate\Support\Facades\Log::info('TOGGLE DEBUG - Tokens revoked', ['user_id' => $id]);
        }
        
        $saved = $user->save();
        \Illuminate\Support\Facades\Log::info('TOGGLE DEBUG - Save completed', ['success' => $saved]);

        // ✅ Cascade activation to associated students if the user is a family
        if ($user->role === 'family' && $user->family) {
            $studentUserIds = $user->family->students()->pluck('user_id')->filter();
            if ($studentUserIds->isNotEmpty()) {
                User::whereIn('id', $studentUserIds)->update(['is_approved' => $user->is_approved]);
                
                // If deactivating family, also revoke student tokens
                if (!$user->is_approved) {
                    foreach ($studentUserIds as $sId) {
                        $sUser = User::find($sId);
                        if ($sUser) $sUser->tokens()->delete();
                    }
                }
            }
        }

        $statusMessage = $user->is_approved ? 'تم تفعيل الحساب بنجاح' : 'تم إيقاف تفعيل الحساب بنجاح';

        return $this->successResponse(new \Modules\Users\Http\Resources\UserResource($user), $statusMessage, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}/reset-password",
     *     summary="إعادة تعيين كلمة المرور للقيمة الافتراضية",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف المستخدم",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم إعادة التعيين بنجاح"
     *     )
     * )
     */
    public function resetPassword($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->error('المستخدم غير موجود', 404);
        }

        $user->update([
            'password' => Hash::make('12345678'),
            'force_password_change' => true,
        ]);

        // إبطال جميع التوكنات لإجبار المستخدم على تسجيل الدخول بكلمة المرور الجديدة
        $user->tokens()->delete();

        return $this->successResponse(null, 'تم إعادة تعيين كلمة المرور بنجاح إلى 12345678 وإجبار المستخدم على تغييرها عند الدخول.', 200);
    }
}

