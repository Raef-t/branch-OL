<?php

use Illuminate\Support\Facades\Route;
use Modules\MessageTemplates\Http\Controllers\MessageTemplatesController;

Route::group([
     'middleware' => ['api', 'auth:sanctum', 'approved','force-password-change'],
    'prefix' => 'message-templates',
    'as' => 'api.message-templates.',
], function () {
    Route::get('/', [MessageTemplatesController::class, 'index'])->name('index');
    Route::post('/', [MessageTemplatesController::class, 'store'])->name('store');
    Route::get('/{id}', [MessageTemplatesController::class, 'show'])->name('show');
    Route::put('/{id}', [MessageTemplatesController::class, 'update'])->name('update');
    Route::delete('/{id}', [MessageTemplatesController::class, 'destroy'])->name('destroy');
});