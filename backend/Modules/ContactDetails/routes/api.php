<?php

use Illuminate\Support\Facades\Route;
use Modules\ContactDetails\Http\Controllers\ContactDetailsController;
use Modules\ContactDetails\Models\ContactDetail; // ← أضف هذا

Route::group([
      'middleware' => ['api', 'auth:sanctum', 'approved','force-password-change'],
    'prefix' => 'contact-details',
    'as' => 'api.contact-details.',
], function () {
    Route::get('/', [ContactDetailsController::class, 'index'])->name('index');
    Route::post('/', [ContactDetailsController::class, 'store'])->name('store');
    Route::get('/{contactDetail}', [ContactDetailsController::class, 'show'])->name('show'); // ✅
    Route::put('/{contactDetail}', [ContactDetailsController::class, 'update'])->name('update'); // ✅
    Route::delete('/{contactDetail}', [ContactDetailsController::class, 'destroy'])->name('destroy'); // ✅

    // 🟢 ميزة جديدة: جلب كافة معلومات التواصل المرتبطة بالطالب (شخصي، عائلي، أولياء أمور)
    Route::get('/student/{student_id}', [ContactDetailsController::class, 'getStudentContactsSummary'])->name('student-summary');
});