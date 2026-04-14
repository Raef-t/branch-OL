<?php

use Illuminate\Support\Facades\Route;
use Modules\Employees\Http\Controllers\EmployeeActivationController;
use Modules\Employees\Http\Controllers\EmployeesController;

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix' => 'employees',
    'as' => 'api.employees.',
], function () {

    // CRUD
    Route::get('/', [EmployeesController::class, 'index'])->name('index');

    // 🔒 Admin only routes
    Route::middleware(['role:admin'])->group(function () {
        Route::post('/', [EmployeesController::class, 'store'])->name('store');
        Route::get('/count', [EmployeesController::class, 'count']);
        Route::delete('/{id}', [EmployeesController::class, 'destroy'])->name('destroy');
    });

 // تفعيل المستخدم
    Route::post(
        '/{employee}/activate-user',
        EmployeeActivationController::class
    )->name('activate-user');

    Route::get('/with-assignments', [EmployeesController::class, 'indexWithAssignments'])
        ->name('with-assignments');
    Route::get('/{id}', [EmployeesController::class, 'show'])->name('show');
    Route::put('/{id}', [EmployeesController::class, 'update'])->name('update');

    // رفع صورة
    Route::post('/{id}/photo', [EmployeesController::class, 'uploadPhoto'])->name('upload-photo');

   

    Route::post('/{id}/assign-to-batch', [EmployeesController::class, 'updateAssignmentWithPost'])
        ->name('assign-to-batch');

    Route::delete('/{id}/assignments/{batch_id}', [EmployeesController::class, 'deleteBatchAssignment'])
        ->name('delete-batch-assignment');

    // راوت إضافي إن كان موجود أصلًا
    Route::post('/assign-to-batch', [EmployeesController::class, 'storeForBatch'])->name('assign-to-batch-old');
  

});
