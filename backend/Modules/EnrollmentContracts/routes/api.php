<?php

use Illuminate\Support\Facades\Route;
use Modules\EnrollmentContracts\Http\Controllers\EnrollmentContractsController;

Route::group([
      'middleware' => ['api', 'auth:sanctum', 'approved','force-password-change'],
    'prefix' => 'enrollment-contracts',
    'as' => 'api.enrollment-contracts.',
], function () {
    Route::get('/', [EnrollmentContractsController::class, 'index'])->name('index');
    Route::post('/', [EnrollmentContractsController::class, 'store'])->name('store');
    Route::post('/preview', [EnrollmentContractsController::class, 'preview'])->name('preview');
    Route::get('/{id}', [EnrollmentContractsController::class, 'show'])->name('show');
    Route::put('/{id}', [EnrollmentContractsController::class, 'update'])->name('update');
    Route::delete('/{id}', [EnrollmentContractsController::class, 'destroy'])->name('destroy');
});