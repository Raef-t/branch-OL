<?php

use Illuminate\Support\Facades\Route;
use Modules\InstituteBranches\Http\Controllers\InstituteBranchesController;

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix' => 'institute-branches',
    'as' => 'api.institute-branches.',
], function () {
    Route::get('/', [InstituteBranchesController::class, 'index'])->name('index');
    Route::post('/', [InstituteBranchesController::class, 'store'])->name('store');
    Route::get('/{id}', [InstituteBranchesController::class, 'show'])->name('show');
    Route::put('/{id}', [InstituteBranchesController::class, 'update'])->name('update');
    Route::delete('/{id}', [InstituteBranchesController::class, 'destroy'])->name('destroy');
});
