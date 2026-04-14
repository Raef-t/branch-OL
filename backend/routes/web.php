<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\DB;

Route::get('/test-audit', function () {
    $audit = DB::table('audits')->first();
    $decoded = json_decode($audit->new_values, true);
    return $decoded;
});

Route::get('/debug-parent-dashboard', function () {
    $family = \Modules\Families\Models\Family::with([
        'students.latestActiveEnrollmentContract',
        'students.latestBatchStudent.batch',
        'guardians'
    ])->first();

    if (!$family) return 'No family found in database.';

    return new \Modules\Guardians\Http\Resources\GuardianDashboardResource($family);
});

Route::get('/', function () {
    return view('welcome');
});
