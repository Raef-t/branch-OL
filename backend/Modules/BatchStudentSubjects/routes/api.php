<?php

use Illuminate\Support\Facades\Route;
use Modules\BatchStudentSubjects\Http\Controllers\BatchStudentSubjectController;

Route::middleware([
        'api',
        'auth:sanctum',
    ])
    ->prefix('batch-student-subjects')
    ->as('api.batch-student-subjects.')
    ->group(function () {

        /**
         * إضافة / ربط مواد لطالب ضمن دفعة (طالب جزئي)
         */
        Route::post('/', [BatchStudentSubjectController::class, 'store'])
            ->name('store');

        /**
         * تحديث حالة مادة لطالب
         */
        Route::put('{id}', [BatchStudentSubjectController::class, 'updateStatus'])
            ->name('update-status');

        /**
         * حذف مادة من طالب (إلغاء الربط)
         */
        Route::delete('{id}', [BatchStudentSubjectController::class, 'destroy'])
            ->name('destroy');
    });
