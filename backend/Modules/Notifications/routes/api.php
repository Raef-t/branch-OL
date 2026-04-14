<?php

use Illuminate\Support\Facades\Route;
use Modules\Notifications\Http\Controllers\NotificationsController;

/*
|--------------------------------------------------------------------------
| واجهات برمجة تطبيقات الإشعارات
|--------------------------------------------------------------------------
|
| 📌 تقسيم المسارات:
| 1. /notifications/* → واجهات المستخدم العادي
| 2. /admin/notifications/* → واجهات الإدارة (تتطلب صلاحيات إضافية)
|
| 🔒 الحماية:
| - جميع المسارات تتطلب: auth:sanctum, approved, force-password-change
| - مسارات الإدارة تتطلب إضافة: صلاحية 'manage-notifications'
|
*/

// ========================================
// 🔹 واجهات المستخدم العادي
// ========================================
Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix' => 'notifications',
    'as' => 'api.notifications.',
], function () {

    // إنشاء إشعار (للإدارة فقط - سيُحمى بـ Policy لاحقًا)
    Route::post('/', [NotificationsController::class, 'store'])
        ->name('store');

    // جلب إشعارات المستخدم
    Route::get('/', [NotificationsController::class, 'index'])
        ->name('index');

    // بحث في إشعارات المستخدم
    Route::get('/search', [NotificationsController::class, 'search'])
        ->name('search');

    // تصفية حسب التاريخ
    Route::get('/filter-by-date', [NotificationsController::class, 'filterByDate'])
        ->name('filter-by-date');

    // عدد الإشعارات غير المقروءة
    Route::get('unread/count', [NotificationsController::class, 'unreadCount'])
        ->name('unread-count');

    // تعليم جميع الإشعارات كمقروءة
    Route::post('mark-all-as-read', [NotificationsController::class, 'markAllAsRead'])
        ->name('mark-all-as-read');

    // جلب تفاصيل إشعار
    Route::get('{reception}', [NotificationsController::class, 'show'])
        ->name('show');

    // تعليم إشعار كمقروء
    Route::patch('{reception}/read', [NotificationsController::class, 'markAsRead'])
        ->name('read');

    // حذف إشعار من قائمة المستخدم
    Route::delete('{reception}', [NotificationsController::class, 'destroy'])
        ->name('destroy');
});

// ========================================
// 🔹 واجهات الإدارة (بصلاحيات إضافية)
// ========================================
Route::group([
    'middleware' => [
        'api',
        'auth:sanctum',
        'approved',
        'force-password-change',
    ],
    'prefix' => 'admin/notifications',
    'as' => 'api.admin.notifications.',
], function () {

    // عرض جميع الإشعارات في النظام مع فلترة متقدمة
    Route::get('/', [NotificationsController::class, 'adminIndex'])
        ->name('index');

    // عرض تفاصيل إشعار مع جميع المستلمين (للإدارة)
    Route::get('/{notification}', [NotificationsController::class, 'adminShow'])
        ->name('show');

    // إعادة إرسال إشعار (للإدارة)
    Route::post('/{notification}/resend', [NotificationsController::class, 'adminResend'])
        ->name('resend');

    // حذف إشعار من النظام بالكامل (للإدارة)
    Route::delete('/{notification}', [NotificationsController::class, 'adminDestroy'])
        ->name('destroy');
});
