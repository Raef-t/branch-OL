<?php

use Illuminate\Support\Facades\Route;
use Modules\ExamResults\Http\Controllers\ExamResultsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('examresults', ExamResultsController::class)->names('examresults');
});
