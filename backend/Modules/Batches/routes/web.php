<?php

use Illuminate\Support\Facades\Route;
use Modules\Batches\Http\Controllers\BatchesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('batches', BatchesController::class)->names('batches');
});
