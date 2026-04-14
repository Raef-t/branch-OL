<?php

use Illuminate\Support\Facades\Route;
use Modules\Instructors\Http\Controllers\InstructorsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('instructors', InstructorsController::class)->names('instructors');
});
