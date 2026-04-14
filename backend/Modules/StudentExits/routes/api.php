<?php

use Illuminate\Support\Facades\Route;
use Modules\StudentExits\Http\Controllers\StudentExitLogController;

Route::middleware(['api', 'auth:sanctum'])
    ->prefix('student-exits')
    ->group(function () {

        Route::get('/', [StudentExitLogController::class, 'index']);
        Route::get('/latest', [StudentExitLogController::class, 'latest']);
        Route::post('/', [StudentExitLogController::class, 'store']);
        Route::post('/bulk', [StudentExitLogController::class, 'bulkStore']);
        Route::get('/{id}', [StudentExitLogController::class, 'show']);
        Route::put('/{id}', [StudentExitLogController::class, 'update']);
        Route::delete('/{id}', [StudentExitLogController::class, 'destroy']);
    });
