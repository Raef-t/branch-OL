<?php

use Illuminate\Support\Facades\Route;
use Modules\StudentStatuses\Http\Controllers\StudentStatusesController;

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix' => 'student-statuses',
    'as' => 'api.student-statuses.',
], function () {
    Route::get('/', [StudentStatusesController::class, 'index'])->name('index');
    Route::post('/', [StudentStatusesController::class, 'store'])->name('store');
    Route::get('/{id}', [StudentStatusesController::class, 'show'])->name('show');
    Route::put('/{id}', [StudentStatusesController::class, 'update'])->name('update');
    Route::delete('/{id}', [StudentStatusesController::class, 'destroy'])->name('destroy');
});
