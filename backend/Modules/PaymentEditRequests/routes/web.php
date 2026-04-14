<?php

use Illuminate\Support\Facades\Route;
use Modules\PaymentEditRequests\Http\Controllers\PaymentEditRequestsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('paymenteditrequests', PaymentEditRequestsController::class)->names('paymenteditrequests');
});
