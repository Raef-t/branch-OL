<?php

use Illuminate\Support\Facades\Route;
use Modules\ExamResultEditRequests\Http\Controllers\ExamResultEditRequestsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('examresulteditrequests', ExamResultEditRequestsController::class)->names('examresulteditrequests');
});
