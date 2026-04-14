<?php

use Illuminate\Support\Facades\Route;
use Modules\AuthorizedDevices\Http\Controllers\AuthorizedDevicesController;

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix' => 'authorized-devices',
    'as' => 'api.authorized-devices.',
], function () {
    Route::get('/', [AuthorizedDevicesController::class, 'index'])->name('index');
    Route::post('/', [AuthorizedDevicesController::class, 'store'])->name('store');
    Route::get('/{id}', [AuthorizedDevicesController::class, 'show'])->name('show');
    Route::put('/{id}', [AuthorizedDevicesController::class, 'update'])->name('update');
    Route::delete('/{id}', [AuthorizedDevicesController::class, 'destroy'])->name('destroy');
});
