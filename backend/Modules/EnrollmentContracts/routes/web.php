<?php

use Illuminate\Support\Facades\Route;
use Modules\EnrollmentContracts\Http\Controllers\EnrollmentContractsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('enrollmentcontracts', EnrollmentContractsController::class)->names('enrollmentcontracts');
});
