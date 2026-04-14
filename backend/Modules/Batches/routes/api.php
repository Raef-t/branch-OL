<?php

use Illuminate\Support\Facades\Route;
use Modules\Batches\Http\Controllers\BatchesController;
use Modules\Batches\Http\Controllers\BatchPerformanceController;

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix' => 'batches',
    'as'     => 'api.batches.',
], function () {

    /*
    |--------------------------------------------------------------------------
    | Static Routes (يجب أن تأتي قبل /{id} لمنع التعارض)
    |--------------------------------------------------------------------------
    */
    Route::get('/', [BatchesController::class, 'index'])->name('index');

    // 🔒 Admin only routes
    Route::middleware(['role:admin'])->group(function () {
        Route::post('/', [BatchesController::class, 'store'])->name('store');
        Route::put('/{id}', [BatchesController::class, 'update'])->name('update');
        Route::get('/stats', [BatchesController::class, 'getStats'])->name('stats');
        Route::get('/averages', [BatchesController::class, 'getBatchesAverages'])->name('batches.averages');
        
        Route::get('/performance/all', [BatchPerformanceController::class, 'index'])
            ->name('performance.index');
        Route::get('/performance/top', [BatchPerformanceController::class, 'top'])
            ->name('performance.top');
        Route::delete('/{id}', [BatchesController::class, 'destroy'])->name('destroy');
    });

    Route::get('/{id}', [BatchesController::class, 'show'])->name('show');

    Route::get('/exam-results/exam/last-two-weeks', [BatchesController::class, 'examResultsLastTwoWeeks']);

    Route::get('/{id}/exams', [BatchesController::class, 'getExams'])
        ->name('exams');

    Route::get('/{batch_id}/exams/last-two-weeks', [BatchesController::class, 'examsLastTwoWeeks']);

    Route::get('/{id}/with-teachers', [BatchesController::class, 'showWithTeachers'])
        ->name('showWithTeachers');

    Route::get('/{id}/with-supervisor', [BatchesController::class, 'showWithSupervisor'])
        ->name('showWithSupervisor');

    Route::get('/{batch}/students/last-attendance', [
        BatchesController::class,
        'batchLastAttendance'
    ])->name('students.lastAttendance');
});

