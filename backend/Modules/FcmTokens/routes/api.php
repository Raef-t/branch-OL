<?php
use Illuminate\Support\Facades\Route;
use Modules\FcmTokens\Http\Controllers\FcmTokenController;

Route::group([
    'middleware' => ['api'],
    'prefix' => 'fcm-tokens',
    'as' => 'api.fcm-tokens.',
], function () {
    Route::get('/', [FcmTokenController::class, 'index']);
    Route::post('/', [FcmTokenController::class, 'store']);
    Route::put('/{id}', [FcmTokenController::class, 'update']);
    Route::delete('/{id}', [FcmTokenController::class, 'destroy']);
});
