<?php

use Illuminate\Support\Facades\Route;
use Modules\Schools\Http\Controllers\SchoolsController;

Route::group([
    'middleware' => ['api', 'auth:sanctum'],
    'prefix' => 'schools',
], function () {

    // 📌 جلب جميع المدارس
    Route::get('/', [SchoolsController::class, 'index']);

    // 📌 إنشاء مدرسة جديدة
    Route::post('/', [SchoolsController::class, 'store']);

    // 📌 عرض مدرسة واحدة
    Route::get('{school}', [SchoolsController::class, 'show']);

    // 📌 تحديث مدرسة
    Route::put('{school}', [SchoolsController::class, 'update']);

    // 📌 حذف مدرسة
    Route::delete('{school}', [SchoolsController::class, 'destroy']);
});
