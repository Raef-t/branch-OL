<?php

use Illuminate\Support\Facades\Route;
use Modules\Settings\Http\Controllers\SettingsController;

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix' => 'settings',
    'as' => 'api.settings.',
], function () {

    // جلب إعدادات النظام
    Route::get('/', [SettingsController::class, 'index'])->name('index');

    // تحديث إعدادات النظام (تشغيل / إيقاف + رسالة النظام)
    Route::put('/', [SettingsController::class, 'update'])->name('update');

    // أخذ نسخة احتياطية لقاعدة البيانات
    Route::get('/backup', [SettingsController::class, 'backup'])->name('backup');

    // استعادة قاعدة البيانات (للمدراء فقط)
    Route::post('/restore', [SettingsController::class, 'restore'])->middleware('role:admin')->name('restore');

});
