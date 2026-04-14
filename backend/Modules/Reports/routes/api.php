<?php

use Illuminate\Support\Facades\Route;
use Modules\Reports\Http\Controllers\ReportsController;

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix' => 'reports',
    'as'     => 'api.reports.',
], function () {
    Route::get('students', [ReportsController::class, 'index']);
    Route::get('students/attendanceReport', [ReportsController::class, 'attendanceReport']);
});
