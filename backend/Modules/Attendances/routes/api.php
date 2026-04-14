<?php

use Illuminate\Support\Facades\Route;
use Modules\Attendances\Http\Controllers\AttendancesController;
use Modules\Attendances\Http\Controllers\AttendancesStatsController;

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix' => 'attendance',
    'as' => 'api.attendance.',
], function () {

    // ======================
    // CRUD الحضور
    // ======================
    Route::get('/', [AttendancesController::class, 'index'])->name('index');
    Route::get('/latest', [AttendancesController::class, 'latest'])->name('latest');
    Route::post('/', [AttendancesController::class, 'store'])->name('store');

    // ⭐ تسجيل حضور يدوي لطالب واحد
    Route::post('/manual', [AttendancesController::class, 'manual'])->name('manual');

    // ⭐ تسجيل حضور جماعي للدفعة
    Route::post('/group', [AttendancesController::class, 'groupAttendance'])->name('group');

    Route::get('/{id}', [AttendancesController::class, 'show'])->name('show');
    Route::put('/{id}', [AttendancesController::class, 'update'])->name('update');
    Route::delete('/{id}', [AttendancesController::class, 'destroy'])->name('destroy');

    // ======================
    // 📊 إحصائيات الحضور والغياب
    // ======================
    Route::get('/stats/summary', [AttendancesStatsController::class, 'index'])
        ->name('stats.summary');
});
