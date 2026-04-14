<?php

use Illuminate\Support\Facades\Route;
use Modules\Subjects\Http\Controllers\SubjectsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('subjects', SubjectsController::class)->names('subjects');
});
