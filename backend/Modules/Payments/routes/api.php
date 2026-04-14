<?php

use Illuminate\Support\Facades\Route;
use Modules\Payments\Http\Controllers\PaymentsController;

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix' => 'payments',
    'as' => 'api.payments.',
], function () {
    Route::get('/', [PaymentsController::class, 'index'])->name('index');
    Route::get('/student-late', [PaymentsController::class, 'lateStudentsInPayment'])->name('payments.student-late');
    Route::get('/latest-per-student', [PaymentsController::class, 'latestPaymentsPerStudent'])->name('latest-per-student');
    Route::get('/{payment_id}/edit-requests', [PaymentsController::class, 'getEditRequestsByPayment']);
    Route::get('/edit-requests', [PaymentsController::class, 'getAllEditRequests']);
    Route::put('/edit-requests/{id}/approve', [PaymentsController::class, 'approveEditRequest']);
    Route::put('/edit-requests/{id}/reject', [PaymentsController::class, 'rejectEditRequest']);
    Route::post('/', [PaymentsController::class, 'store'])->name('store');
    Route::get('/{id}', [PaymentsController::class, 'show'])->name('show');
    Route::put('/{id}', [PaymentsController::class, 'update'])->name('update');
    Route::delete('/{id}', [PaymentsController::class, 'destroy'])->name('destroy');
});
