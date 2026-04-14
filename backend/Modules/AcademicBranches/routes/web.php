<?php

use Illuminate\Support\Facades\Route;
use Modules\AcademicBranches\Http\Controllers\AcademicBranchesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('academicbranches', AcademicBranchesController::class)->names('academicbranches');
});
