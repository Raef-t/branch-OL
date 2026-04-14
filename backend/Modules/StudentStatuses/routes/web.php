<?php

use Illuminate\Support\Facades\Route;
use Modules\StudentStatuses\Http\Controllers\StudentStatusesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('studentstatuses', StudentStatusesController::class)->names('studentstatuses');
});
