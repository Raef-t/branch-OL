<?php

use Illuminate\Support\Facades\Route;
use Modules\AcademicRecords\Http\Controllers\AcademicRecordsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('academicrecords', AcademicRecordsController::class)->names('academicrecords');
});
