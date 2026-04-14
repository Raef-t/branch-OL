<?php

use Illuminate\Support\Facades\Route;
use Modules\BatchSubjects\Http\Controllers\BatchSubjectsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('batchsubjects', BatchSubjectsController::class)->names('batchsubjects');
});
