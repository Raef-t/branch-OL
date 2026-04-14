<?php

use Illuminate\Support\Facades\Route;   
use Modules\DoorSessions\Http\Controllers\DoorSessionsController;
use Modules\DoorSessions\Http\Controllers\GenerateDoorSessionController;
use Modules\DoorSessions\Http\Controllers\UseDoorSessionController;

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved'],
    'prefix' => 'door-sessions',
    'as' => 'api.door-sessions.',
], function () {
    
    Route::get('/', [DoorSessionsController::class, 'index'])->name('index');
    Route::post('/', [DoorSessionsController::class, 'store'])->name('store');
   Route::post('/use', UseDoorSessionController::class)->name('use');

    Route::get('/{id}', [DoorSessionsController::class, 'show'])->name('show');
    Route::put('/{id}', [DoorSessionsController::class, 'update'])->name('update');
    Route::delete('/{id}', [DoorSessionsController::class, 'destroy'])->name('destroy');
});

Route::group([
    'prefix' => 'door-sessions',
    'middleware' => ['api', 'door-device-auth'], // هذا هو الميدل‌وير الذي تحقق من api_key
], function () {
    Route::post('/generate', GenerateDoorSessionController::class)->name('generate');
});
