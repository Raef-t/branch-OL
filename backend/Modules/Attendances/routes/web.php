<?php

use Illuminate\Support\Facades\Route;
use Modules\Attendances\Http\Controllers\AttendancesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('attendances', AttendancesController::class)->names('attendances');
});
