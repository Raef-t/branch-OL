<?php

use Illuminate\Support\Facades\Route;
use Modules\AcademicBranches\Http\Controllers\AcademicBranchesController;

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved','force-password-change'],
    'prefix' => 'academic-branches',
    'as' => 'api.academic-branches.',
], function () {
    Route::get('/', [AcademicBranchesController::class, 'index']);
    Route::get('/{genderType?}', [AcademicBranchesController::class, 'getDetailsForAcadimicBranches']);
    Route::get('/{id}/subjects', [AcademicBranchesController::class, 'getSubjects']);
    Route::get('/{id}', [AcademicBranchesController::class, 'show']);
    Route::post('/', [AcademicBranchesController::class, 'store']);
    Route::put('/{id}', [AcademicBranchesController::class, 'update']);
    Route::delete('/{id}', [AcademicBranchesController::class, 'destroy']);
});
