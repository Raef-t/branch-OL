<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Modules\Settings\Models\Setting;

class CheckSystemStatus
{
    public function handle($request, Closure $next)
    {
        // ✨ 1) Allow List لمسارات معينة (login, settings, health, إلخ)
        $allowedRoutes = [
            'api/auth/login',
            'api/admin/login',
            'api/settings*',
            'up'
        ];

        foreach ($allowedRoutes as $route) {
            if ($request->is($route)) {
                return $next($request);
            }
        }

        /** @var \Modules\Users\Models\User $user */
        $user = Auth::guard('sanctum')->user();

        if ($user && $user->hasRole('admin')) {
            return $next($request);
        }


        // 2. التحقق من حالة النظام
        try {
            $setting = Setting::first();
            $isSystemEnabled = $setting ? $setting->is_system_enabled : true;
            $maintenanceMessage = $setting ? $setting->maintenance_message : null;
        } catch (\Exception $e) {
            // في حال عدم وجود الجدول أو أي خطأ في قاعدة البيانات، نفترض أن النظام مفعل
            $isSystemEnabled = true;
            $maintenanceMessage = null;
        }

        if (!$isSystemEnabled) {

            $message = $maintenanceMessage
                ? $maintenanceMessage
                : 'النظام متوقف حالياً من قبل الإدارة'; 

            return response()->json([
                'message' => $message
            ], 503);
        }

        return $next($request);
    }
}
