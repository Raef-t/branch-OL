<?php

use Illuminate\Support\Facades\Route;
use Modules\Exams\Http\Controllers\ExamsController;
use Modules\Exams\Http\Controllers\ExamsStatisticsController;
use Modules\Exams\Http\Controllers\StudentMessageController;
use Modules\Exams\Http\Controllers\AttendanceVerificationController; // ✅ أضف هذا الاستيراد

Route::group([
    // 'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix' => 'exams',
    'as' => 'api.exams.',
], function () {
    Route::get('/statistics/top-performers', [ExamsStatisticsController::class, 'getTopPerformers'])
        ->name('statistics.top-performers');
    // ... المسارات الحالية ...

    Route::get('/filtered', [ExamsController::class, 'getFilteredExams'])
        ->name('filtered');

    Route::post('/{id}/complete', [ExamsController::class, 'complete'])
        ->name('complete');

    Route::post('/{id}/postpone', [ExamsController::class, 'postpone'])
        ->name('postpone');

    Route::post('/{id}/cancel', [ExamsController::class, 'cancel'])
        ->name('cancel');

    Route::get('/attendance-verification', [AttendanceVerificationController::class, 'index'])
        ->name('attendance-verification.index');


    Route::get('/', [ExamsController::class, 'index'])->name('index');
    Route::post('/', [ExamsController::class, 'store'])->name('store');
    Route::post('/student-messages', [StudentMessageController::class, 'store']);
    Route::get('/{id}', [ExamsController::class, 'show'])->name('show')->where('id', '[0-9]+');
    Route::get('/{date}', [ExamsController::class, 'getExamsByDate'])->name('exams.by-date')->where('date', '[0-9]{4}-[0-9]{2}-[0-9]{2}');
    Route::put('/{id}', [ExamsController::class, 'update'])->name('update');
    Route::delete('/{id}', [ExamsController::class, 'destroy'])->name('destroy');
});
