<?php

use Illuminate\Support\Facades\Route;
use Modules\ExamTypes\Http\Controllers\ExamTypesController;

Route::group([
    'middleware' => ['api'],
    'prefix' => 'exam-types',
    'as' => 'api.exam-types.',
], function () {
    Route::get('/', [ExamTypesController::class, 'index'])->name('index');
    Route::post('/', [ExamTypesController::class, 'store'])->name('store');
    Route::get('/{id}', [ExamTypesController::class, 'show'])->name('show');
    Route::put('/{id}', [ExamTypesController::class, 'update'])->name('update');
    Route::delete('/{id}', [ExamTypesController::class, 'destroy'])->name('destroy');
});