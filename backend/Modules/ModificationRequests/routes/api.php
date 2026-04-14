<?php

use Illuminate\Support\Facades\Route;
use Modules\ModificationRequests\Http\Controllers\ModificationRequestsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('modificationrequests', ModificationRequestsController::class)->names('modificationrequests');
});
