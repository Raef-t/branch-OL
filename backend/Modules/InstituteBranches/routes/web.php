<?php

use Illuminate\Support\Facades\Route;
use Modules\InstituteBranches\Http\Controllers\InstituteBranchesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('institutebranches', InstituteBranchesController::class)->names('institutebranches');
});
