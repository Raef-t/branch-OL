<?php

use Illuminate\Support\Facades\Route;
use Modules\DoorDevices\Http\Controllers\DoorDevicesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('doordevices', DoorDevicesController::class)->names('doordevices');
});
