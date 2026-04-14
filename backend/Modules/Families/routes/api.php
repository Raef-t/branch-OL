<?php

use Illuminate\Support\Facades\Route;
use Modules\Families\Http\Controllers\FamiliesController;
use Modules\Families\Http\Controllers\FamilyActivationController;

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix' => 'families',
    'as' => 'api.families.',
], function () {
    Route::get('/', [FamiliesController::class, 'index'])->name('index');
    Route::post('/', [FamiliesController::class, 'store'])->name('store');
    Route::get('/{id}', [FamiliesController::class, 'show'])->name('show');
    Route::put('/{id}', [FamiliesController::class, 'update'])->name('update');
    Route::delete('/{id}', [FamiliesController::class, 'destroy'])->name('destroy');
    Route::post('/{family}/activate-user', FamilyActivationController::class)
        ->name('families.activate-user');
        
    Route::get('/me/financial-summary', [FamiliesController::class, 'myFamilyFinancialSummary'])
        ->name('me.financial-summary');
});
