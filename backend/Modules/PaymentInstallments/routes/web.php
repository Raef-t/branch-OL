<?php

use Illuminate\Support\Facades\Route;
use Modules\PaymentInstallments\Http\Controllers\PaymentInstallmentsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('paymentinstallments', PaymentInstallmentsController::class)->names('paymentinstallments');
});
