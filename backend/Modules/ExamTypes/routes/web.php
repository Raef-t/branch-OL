<?php

use Illuminate\Support\Facades\Route;
use Modules\ExamTypes\Http\Controllers\ExamTypesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('examtypes', ExamTypesController::class)->names('examtypes');
});
