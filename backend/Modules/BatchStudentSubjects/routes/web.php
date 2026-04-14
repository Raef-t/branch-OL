<?php

use Illuminate\Support\Facades\Route;
use Modules\BatchStudentSubjects\Http\Controllers\BatchStudentSubjectController;
 
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('batchstudentsubjects', BatchStudentSubjectController::class)->names('batchstudentsubjects');
});
