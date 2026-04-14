<?php

use Illuminate\Support\Facades\Route;
use Modules\ClassSchedules\Http\Controllers\ClassSchedulesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('classschedules', ClassSchedulesController::class)->names('classschedules');
});
