<?php

use Illuminate\Support\Facades\Route;
use Modules\Families\Http\Controllers\FamiliesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('families', FamiliesController::class)->names('families');
});
