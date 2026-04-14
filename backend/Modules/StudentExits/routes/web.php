<?php

use Illuminate\Support\Facades\Route;
use Modules\StudentExits\Http\Controllers\StudentExitsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('studentexits', StudentExitsController::class)->names('studentexits');
});
