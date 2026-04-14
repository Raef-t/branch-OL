<?php

use Illuminate\Support\Facades\Route;
use Modules\AcademicRecords\Http\Controllers\AcademicRecordsController;

Route::group([
       'middleware' => ['api', 'auth:sanctum', 'approved','force-password-change'],

    'prefix' => 'academic-records',
    'as' => 'api.academic-records.',
], function () {
    Route::get('/', [AcademicRecordsController::class, 'index'])->name('index');
    Route::post('/', [AcademicRecordsController::class, 'store'])->name('store');
    Route::get('/{id}', [AcademicRecordsController::class, 'show'])->name('show');
    Route::put('/{id}', [AcademicRecordsController::class, 'update'])->name('update');
    Route::delete('/{id}', [AcademicRecordsController::class, 'destroy'])->name('destroy');
});