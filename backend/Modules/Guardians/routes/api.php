<?php

use Illuminate\Support\Facades\Route;
use Modules\Guardians\Http\Controllers\GuardiansController;

Route::group([
       'middleware' => ['api', 'auth:sanctum', 'approved','force-password-change'],
    'prefix' => 'guardians',
    'as' => 'api.guardians.',
], function () {
    Route::get('/dashboard', [\Modules\Guardians\Http\Controllers\GuardianDashboardController::class, 'dashboard']);
    Route::get('/', [GuardiansController::class, 'index'])->name('index');
    Route::post('/', [GuardiansController::class, 'store'])->name('store');
    
    // 🔒 Admin only routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/total-guardians', [GuardiansController::class, 'totalGuardians']);
    });

    Route::get('/{id}', [GuardiansController::class, 'show'])->name('show');
    Route::put('/{id}', [GuardiansController::class, 'update'])->name('update');
    Route::delete('/{id}', [GuardiansController::class, 'destroy'])->name('destroy');
});