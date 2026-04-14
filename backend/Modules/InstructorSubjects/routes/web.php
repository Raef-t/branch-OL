<?php

use Illuminate\Support\Facades\Route;
use Modules\InstructorSubjects\Http\Controllers\InstructorSubjectsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('instructorsubjects', InstructorSubjectsController::class)->names('instructorsubjects');
});
