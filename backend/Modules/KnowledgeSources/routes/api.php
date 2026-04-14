<?php

use Illuminate\Support\Facades\Route;
use Modules\KnowledgeSources\Http\Controllers\KnowledgeSourceController;

Route::group([
    'middleware' => [
        'api',
        'auth:sanctum',
        'approved',
        'force-password-change'
    ],
    'prefix' => 'knowledge-sources',
    'as' => 'api.knowledge-sources.',
], function () {

    // 🔹 جلب جميع طرق المعرفة
    Route::get('/', [KnowledgeSourceController::class, 'index'])
        ->name('index');

    // 🔹 إضافة طريقة معرفة جديدة
    Route::post('/', [KnowledgeSourceController::class, 'store'])
        ->name('store');

    // 🔹 عرض طريقة معرفة واحدة
    Route::get('/{id}', [KnowledgeSourceController::class, 'show'])
        ->name('show');

    // 🔹 تعديل طريقة معرفة
    Route::put('/{id}', [KnowledgeSourceController::class, 'update'])
        ->name('update');

    // 🔹 حذف طريقة معرفة
    Route::delete('/{id}', [KnowledgeSourceController::class, 'destroy'])
        ->name('destroy');

    // 🔹 تفعيل / تعطيل طريقة معرفة
    Route::patch('/{id}/toggle', [KnowledgeSourceController::class, 'toggleStatus'])
        ->name('toggle');
});
