<?php

use Illuminate\Support\Facades\Route;
use Modules\ModificationRequests\Http\Controllers\ModificationRequestsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('modificationrequests', ModificationRequestsController::class)->names('modificationrequests');
});
