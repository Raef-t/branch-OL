<?php

use App\Models\Subjects;
use Illuminate\Support\Facades\Route;
use Modules\Subjects\Http\Controllers\SubjectsController;

Route::group([
      'middleware' => ['api', 'auth:sanctum', 'approved','force-password-change'],
    'prefix' => 'subjects',
    'as' => 'api.subjects.',
], function () {
    Route::get('/', [SubjectsController::class, 'index'])->name('index');
    Route::post('/', [SubjectsController::class, 'store'])->name('store');
    Route::get('/{id}', [SubjectsController::class, 'show'])->name('show');
    Route::put('/{id}', [SubjectsController::class, 'update'])->name('update');
    Route::delete('/{id}', [SubjectsController::class, 'destroy'])->name('destroy');
});
