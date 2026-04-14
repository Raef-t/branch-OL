<?php

use Illuminate\Support\Facades\Route;
use Modules\Instructors\Http\Controllers\InstructorsController;

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix' => 'teachers',
    'as' => 'api.teachers.',
], function () {

    // 🔹 جلب جميع المدرسين مع الدورات التي يدرّسون فيها (جماعي)
    Route::get('/batches-details', [
        InstructorsController::class,
        'getAllTeachersBatchesDetails'
    ])->name('batches-details');

    // 🔹 CRUD المدرسين
    Route::get('/', [InstructorsController::class, 'index'])->name('index');
    Route::post('/', [InstructorsController::class, 'store'])->name('store');

    // 🔹 تفاصيل دورات مدرس محدد
    Route::get('/{id}/batches-details', [
        InstructorsController::class,
        'getBatchesDetails'
    ])->name('single-batches-details');

    Route::get('/{id}', [InstructorsController::class, 'show'])->name('show');
    Route::put('/{id}', [InstructorsController::class, 'update'])->name('update');
    Route::delete('/{id}', [InstructorsController::class, 'destroy'])->name('destroy');

    Route::post('/{id}/photo', [
        InstructorsController::class,
        'updatePhoto'
    ])->name('updatePhoto');
});
