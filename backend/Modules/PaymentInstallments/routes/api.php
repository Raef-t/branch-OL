<?php

use Illuminate\Support\Facades\Route;
use Modules\PaymentInstallments\Http\Controllers\PaymentInstallmentsController;

Route::group([
       'middleware' => ['api', 'auth:sanctum', 'approved','force-password-change'],
    'prefix' => 'payment-installments',
    'as' => 'api.payment-installments.',
], function () {
    Route::get('/', [PaymentInstallmentsController::class, 'index'])->name('index');
    Route::post('/', [PaymentInstallmentsController::class, 'store'])->name('store');
    Route::get('/{id}', [PaymentInstallmentsController::class, 'show'])->name('show');
    Route::put('/{id}', [PaymentInstallmentsController::class, 'update'])->name('update');
    Route::delete('/{id}', [PaymentInstallmentsController::class, 'destroy'])->name('destroy');
});