<?php

use Illuminate\Support\Facades\Route;
use Modules\Students\Http\Controllers\StudentsController;
use Modules\Students\Http\Controllers\StudentReportController;
use Modules\Students\Http\Controllers\StudentAttendanceController;

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix' => 'students',
    'as' => 'api.students.',
], function () {

    Route::get('/schedules', [StudentsController::class, 'getSchedule'])
        ->name('students.schedules.index');

    // routes/api.php
    Route::get('/{studentID}/monthly-evaluation', [StudentsController::class, 'getMonthlyEvaluations']);
    
    // 🔒 Admin only routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/count-per-branch', [StudentsController::class, 'countPerBranch']);
        Route::get('/total-students', [StudentsController::class, 'totalStudents']);
        Route::get('/details', [StudentsController::class, 'indexDetailed']);

        // 📊 Attendance Reports
        Route::prefix('reports/attendance')->group(function () {
            Route::get('/generate', [\Modules\Students\Http\Controllers\AttendanceReportController::class, 'generate']);
            Route::get('/students', [\Modules\Students\Http\Controllers\AttendanceReportController::class, 'getStudentsByBatches']);
        });

        // 📝 Student Data Reports
        Route::prefix('reports/data')->group(function () {
            Route::get('/generate', [\Modules\Students\Http\Controllers\StudentDataReportController::class, 'generate']);
        });

        // 📝 Exam Reports
        Route::prefix('reports/exams')->group(function () {
            Route::get('/generate', [\Modules\Students\Http\Controllers\ExamReportController::class, 'generate']);
        });

        // 📝 Bus Reports
        Route::prefix('reports/buses')->group(function () {
            Route::get('/generate', [\Modules\Students\Http\Controllers\BusReportController::class, 'generate']);
        });

        // 📝 Phone Reports
        Route::prefix('reports/phones')->group(function () {
            Route::get('/generate', [\Modules\Students\Http\Controllers\StudentPhoneReportController::class, 'generate']);
        });
    });

    Route::get('{id}/report', [StudentReportController::class, 'generate']);
    Route::get('{id}/report/download', [StudentReportController::class, 'download']);


    Route::get('/', [StudentsController::class, 'index'])->name('index');
    Route::post('/', [StudentsController::class, 'store'])->name('store');

    Route::get('/{studentID}/weekly-attendance', [StudentAttendanceController::class, 'attendanceLogWeekAndDay']);

    Route::get('/{studentID}/exams/today-and-week', [StudentsController::class, 'currentPeriodExams']);
    Route::get('/{studentID}/exam-results/last-two-weeks', [StudentsController::class, 'lastTwoWeeks']);
    Route::get('/{id}/latest-payment', [StudentsController::class, 'latestPayment']);
    Route::get('/{id}/financial-summary', [StudentsController::class, 'financialSummary']);
    Route::get('/{id}/exams', [StudentsController::class, 'getExamsByStudent']);
    Route::get('/{id}/payments', [StudentsController::class, 'paymentsSummary']);


    Route::post('/{student}/activate-user', \Modules\Students\Http\Controllers\StudentActivationController::class)
        ->name('activate-user');

    Route::get('/{id}/details', [StudentsController::class, 'showStudentDetailed']);
    Route::get('/{id}/profile', [StudentsController::class, 'showProfile']);
    Route::get('/profile', [StudentsController::class, 'profile'])->name('profile');

    Route::get('/{id}', [StudentsController::class, 'show'])->name('show');
    Route::get('/{id}/deletion-report', [StudentsController::class, 'deletionReport'])->name('deletion-report');
    Route::post('/{id}/photos', [StudentsController::class, 'updatePhotos']);
    Route::delete('/{id}', [StudentsController::class, 'destroy'])->name('destroy');
    Route::put('/{id}', [StudentsController::class, 'updateBasic'])->name('update');


    Route::get('/{student}/attendance-log', [StudentAttendanceController::class, 'attendanceLog'])
        ->name('attendance-log');


    Route::put('/{student}/daily-record', [StudentAttendanceController::class, 'updateDailyRecord'])
        ->name('attendance.update-daily');
});
