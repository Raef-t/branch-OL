<?php

use Illuminate\Support\Facades\Route;
use Modules\Buses\Http\Controllers\BusesController;

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix' => 'buses',
    'as' => 'api.buses.',
], function () {
    Route::get('/', [BusesController::class, 'index']);
    Route::get('/{id}/dependencies', [BusesController::class, 'dependencies']);

    Route::get('/{id}', [BusesController::class, 'show']);
    Route::post('/', [BusesController::class, 'store']);
    Route::put('/{id}', [BusesController::class, 'update']);
    Route::get('/{id}/delete-check', [BusesController::class, 'checkDelete']);
    Route::delete('/{id}', [BusesController::class, 'destroy']);
});
