<?php

use Illuminate\Support\Facades\Route;
use Modules\ClassRooms\Http\Controllers\ClassRoomsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('classrooms', ClassRoomsController::class)->names('classrooms');
});
