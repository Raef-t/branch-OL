<?php

use Illuminate\Support\Facades\Route;
use Modules\ExamResults\Http\Controllers\ExamResultsController;

Route::group([
     'middleware' => ['api', 'auth:sanctum', 'approved','force-password-change'],
    'prefix' => 'exam-results',
    'as' => 'api.exam-results.',
], function () {
    Route::get('/', [ExamResultsController::class, 'index'])->name('index');
    Route::get('/edit-requests', [ExamResultsController::class, 'getAllEditRequests'])->name('edit-requests.all');
    Route::get('/student-exam-results', [ExamResultsController::class, 'getStudentExamResults'])->name('student.results');
    Route::get('/filter', [ExamResultsController::class, 'filter'])->name('filter');
    Route::get('/{exam_result_id}/edit-requests', [ExamResultsController::class, 'getEditRequestsByExamResult'])->name('edit-requests.index');
    Route::put('/edit-requests/{id}/approve', [ExamResultsController::class, 'approveEditRequest'])->name('edit-requests.approve');
    Route::put('/edit-requests/{id}/reject', [ExamResultsController::class, 'rejectEditRequest'])->name('edit-requests.reject');
    Route::post('/', [ExamResultsController::class, 'store'])->name('store');
    Route::get('/{id}', [ExamResultsController::class, 'show'])->name('show');
    Route::put('/{id}', [ExamResultsController::class, 'update'])->name('update');
    Route::delete('/{id}', [ExamResultsController::class, 'destroy'])->name('destroy');
    Route::get('/exam/{examId}', [ExamResultsController::class, 'getExamResults'])->name('exam.results');
});