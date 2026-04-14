<?php

use Illuminate\Support\Facades\Route;
use Modules\PaymentEditRequests\Http\Controllers\PaymentEditRequestsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('paymenteditrequests', PaymentEditRequestsController::class)->names('paymenteditrequests');
});
