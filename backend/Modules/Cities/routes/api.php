<?php

use Illuminate\Support\Facades\Route;
use Modules\Cities\Http\Controllers\CitiesController;

Route::group([
       'middleware' => ['api', 'auth:sanctum', 'approved','force-password-change'],
    'prefix' => 'cities', 
    'as' => 'api.cities.',
], function () {
    Route::get('/', [CitiesController::class, 'index']);
    Route::get('/{id}', [CitiesController::class, 'show']);
    Route::post('/', [CitiesController::class, 'store']);
    Route::put('/{id}', [CitiesController::class, 'update']);
    Route::get('/{id}/delete-check', [CitiesController::class, 'checkDelete']);

    Route::delete('/{id}', [CitiesController::class, 'destroy']);
});
