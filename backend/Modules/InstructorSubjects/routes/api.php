<?php

use Illuminate\Support\Facades\Route;
use Modules\InstructorSubjects\Http\Controllers\InstructorSubjectsController;

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix' => 'subjects',
    'as' => 'api.subjects.',
], function () {

    Route::get('/{subject}/teachers', [InstructorSubjectsController::class, 'getInstructorsBySubject'])
        ->name('teachers');

    Route::post('/assign-teacher', [InstructorSubjectsController::class, 'assignInstructorToSubject'])
        ->name('assign-teacher');

    Route::put('/update-teacher-subject/{id}', [InstructorSubjectsController::class, 'updateInstructorSubject'])
        ->name('update-teacher-subject');

    Route::delete('/delete-teacher-subject/{id}', [InstructorSubjectsController::class, 'deleteInstructorSubject'])
        ->name('delete-teacher-subject');
    Route::delete(
        '/delete-teacher-subject-by-ids',
        [InstructorSubjectsController::class, 'deleteInstructorSubjectByIds']
    );
});
