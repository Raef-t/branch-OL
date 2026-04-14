<?php

use Illuminate\Support\Facades\Route;
use Modules\DoorSessions\Http\Controllers\DoorSessionsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('doorsessions', DoorSessionsController::class)->names('doorsessions');
});
