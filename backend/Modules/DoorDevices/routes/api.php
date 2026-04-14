<?php

use Illuminate\Support\Facades\Route;
use Modules\DoorDevices\Http\Controllers\DoorDevicesController;

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix' => 'door-devices',
    'as' => 'api.door-devices.',
], function () {
    Route::get('/', [DoorDevicesController::class, 'index'])->name('index');
    Route::post('/', [DoorDevicesController::class, 'store'])->name('store');
    Route::get('/{id}', [DoorDevicesController::class, 'show'])->name('show');
    Route::put('/{id}', [DoorDevicesController::class, 'update'])->name('update');
    Route::delete('/{id}', [DoorDevicesController::class, 'destroy'])->name('destroy');
});
