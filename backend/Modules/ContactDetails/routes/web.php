<?php

use Illuminate\Support\Facades\Route;
use Modules\ContactDetails\Http\Controllers\ContactDetailsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('contactdetails', ContactDetailsController::class)->names('contactdetails');
});
