<?php

use Illuminate\Support\Facades\Route;
use Modules\ClassSchedules\Http\Controllers\ClassSchedulesController;

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix' => 'class-schedules',
    'as' => 'api.class-schedules.',
], function () {

    // 🔹 جلب جميع جداول الحصص
    Route::get('/', [ClassSchedulesController::class, 'index'])
        ->name('index');

    // 🔹 مسودات الجدولة الذكية
    Route::group(['prefix' => 'drafts'], function () {
        Route::get('/', [\Modules\ClassSchedules\Http\Controllers\ScheduleDraftController::class, 'index']);
        Route::post('/bulk-delete', [\Modules\ClassSchedules\Http\Controllers\ScheduleDraftController::class, 'bulkDestroy']);
        Route::get('/{draftGroupId}', [\Modules\ClassSchedules\Http\Controllers\ScheduleDraftController::class, 'show']);
        Route::post('/{draftGroupId}/publish', [\Modules\ClassSchedules\Http\Controllers\ScheduleDraftController::class, 'publish']);
        Route::delete('/{draftGroupId}', [\Modules\ClassSchedules\Http\Controllers\ScheduleDraftController::class, 'destroy']);
        Route::put('/{draftGroupId}/sync', [\Modules\ClassSchedules\Http\Controllers\ScheduleDraftController::class, 'sync']);
    });

    // 🔹 معالج التوليد الذكي (Wizard)
    Route::group(['prefix' => 'generate'], function () {
        Route::get('/setup', [\Modules\ClassSchedules\Http\Controllers\ScheduleGenController::class, 'getSetupData']);
        Route::post('/start', [\Modules\ClassSchedules\Http\Controllers\ScheduleGenController::class, 'startGeneration']);
    });

    // 🔹 جلب برنامج دوام اليوم (حسب day_of_week)
    Route::get('/today', [ClassSchedulesController::class, 'getTodaySchedule'])
        ->name('today');

    // 🔹 إنشاء جدول حصة
    Route::post('/', [ClassSchedulesController::class, 'store'])
        ->name('store');

    // 🔹 عرض جدول حصة محدد
    Route::get('/{id}', [ClassSchedulesController::class, 'show'])
        ->name('show');

    // 🔹 تحديث جدول حصة
    Route::put('/{id}', [ClassSchedulesController::class, 'update'])
        ->name('update');

    // 🔹 حذف جدول حصة
    Route::delete('/{id}', [ClassSchedulesController::class, 'destroy'])
        ->name('destroy');
});
