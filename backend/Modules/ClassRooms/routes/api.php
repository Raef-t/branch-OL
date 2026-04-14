<?php

use Illuminate\Support\Facades\Route;
use Modules\ClassRooms\Http\Controllers\ClassRoomsController;

Route::group([
    'middleware' => ['api', 'auth:sanctum'],
    'prefix' => 'class-rooms',
    'as' => 'api.class-rooms.',
], function () {
    Route::get('/', [ClassRoomsController::class, 'index'])->name('index');
    Route::post('/', [ClassRoomsController::class, 'store'])->name('store');
    Route::get('/{id}', [ClassRoomsController::class, 'show'])->name('show');
    Route::put('/{id}', [ClassRoomsController::class, 'update'])->name('update');
    Route::delete('/{id}', [ClassRoomsController::class, 'destroy'])->name('destroy');
});
