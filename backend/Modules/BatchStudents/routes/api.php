<?php

use Illuminate\Support\Facades\Route;
use Modules\BatchStudents\Http\Controllers\BatchStudentsController;
use Modules\BatchStudents\Http\Controllers\BulkBatchAssignmentController;

Route::group([
    'middleware' => ['api'],
    'prefix' => 'batch-students',
    'as' => 'api.batch-students.',
], function () {
    Route::get('/', [BatchStudentsController::class, 'index'])->name('index');
    Route::post('/', [BatchStudentsController::class, 'store'])->name('store');

    // مسارات الإضافة الجماعية للطلاب
    Route::get('/unassigned', [BulkBatchAssignmentController::class, 'unassignedStudents'])->name('unassigned');
    Route::post('/bulk-assign', [BulkBatchAssignmentController::class, 'bulkAssign'])->name('bulk-assign');

    Route::get('/{batch_id}/students', [BatchStudentsController::class, 'studentsByBatch'])->where('batch_id', '[0-9]+')->name('students-by-batch');
    Route::get('/{id}', [BatchStudentsController::class, 'show'])->where('id', '[0-9]+')->name('show');
    Route::put('/{id}', [BatchStudentsController::class, 'update'])->where('id', '[0-9]+')->name('update');
    Route::delete('/{id}', [BatchStudentsController::class, 'destroy'])->where('id', '[0-9]+')->name('destroy');
   
});