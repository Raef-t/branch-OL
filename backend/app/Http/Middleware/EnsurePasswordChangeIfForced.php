<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EnsurePasswordChangeIfForced
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = $request->user();

            if ($user && $user->force_password_change && ($user->hasRole('student') || $user->hasRole('family') || $user->hasRole('parent'))) {
                // السماح فقط بطلب تغيير كلمة المرور
                if ($request->is('api/users/change-password')) {
                    return $next($request);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'يجب تغيير كلمة المرور قبل المتابعة.',
                    'data' => [
                        'must_change_password' => true,
                        'redirect_to' => url('/api/users/change-password'),
                    ],
                ], 200);
            }

            return $next($request);

        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ غير متوقع أثناء التحقق من صلاحيات المستخدم.',
                'error' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
