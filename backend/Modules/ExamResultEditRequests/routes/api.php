<?php

use Illuminate\Support\Facades\Route;
use Modules\ExamResultEditRequests\Http\Controllers\ExamResultEditRequestsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('examresulteditrequests', ExamResultEditRequestsController::class)->names('examresulteditrequests');
});
