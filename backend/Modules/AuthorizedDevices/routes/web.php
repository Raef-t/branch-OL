<?php

use Illuminate\Support\Facades\Route;
use Modules\AuthorizedDevices\Http\Controllers\AuthorizedDevicesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('authorizeddevices', AuthorizedDevicesController::class)->names('authorizeddevices');
});
