<?php

use Illuminate\Support\Facades\Route;
use Modules\BatchStudents\Http\Controllers\BatchStudentsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('batchstudents', BatchStudentsController::class)->names('batchstudents');
});
