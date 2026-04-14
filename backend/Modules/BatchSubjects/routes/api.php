<?php

use Illuminate\Support\Facades\Route;
use Modules\BatchSubjects\Http\Controllers\BatchSubjectsController;

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix' => 'batcheSubjects',
    'as' => 'api.batcheSubjects.',
], function () {

    Route::post('/remove-instructor-subject', [BatchSubjectsController::class, 'removeInstructorSubjectFromBatch'])
        ->name('remove-instructor-subject');

    // روت أساسي: جلب المواد المتعلقة بدورة معينة
    Route::get('/{batch}/subjects', [BatchSubjectsController::class, 'getSubjectsByBatch'])
        ->name('subjects');

    // روت أساسي: إضافة المواد التابعة لدورة معينة (تخصيص InstructorSubject لـ Batch)
    Route::post('/assign-instructor-subject', [BatchSubjectsController::class, 'assignInstructorSubjectToBatch'])
        ->name('assign-subject');

    // روت: التحقق من وجود تخصيص مسبقاً (للتحقق قبل الإضافة)
    Route::post('/check-subject-assignment', [BatchSubjectsController::class, 'checkSubjectAssignment'])
        ->name('check-subject-assignment');

    // روت: تعديل تخصيص مادة لدورة
    Route::put('/update-batch-subject/{id}', [BatchSubjectsController::class, 'updateBatchSubject'])
        ->name('update-batch-subject');

    // روت: حذف تخصيص مادة لدورة
    Route::delete('/delete-batch-subject/{id}', [BatchSubjectsController::class, 'deleteBatchSubject'])
        ->name('delete-batch-subject');

    // روت: إلغاء تفعيل تخصيص (بدون حذف، للحفاظ على السجل)
    Route::patch('/deactivate-batch-subject/{id}', [BatchSubjectsController::class, 'deactivateBatchSubject'])
        ->name('deactivate-batch-subject');

    // روت متقدم: جلب جميع التخصيصات النشطة (عامة، للإدارة)
    Route::get('/subjects/all', [BatchSubjectsController::class, 'getAllActiveAssignments'])
        ->name('all-assignments');

    // روت متقدم: جلب تخصيصات لمدرس معين (الدورات والمواد التي يدرسها)
    Route::get('/instructors/{instructor}/assignments', [BatchSubjectsController::class, 'getAssignmentsByInstructor'])
        ->name('instructor-assignments');

    // روت متقدم: جلب تخصيصات لمادة معينة عبر الدورات
    Route::get('/subjects/{subject}/assignments', [BatchSubjectsController::class, 'getAssignmentsBySubject'])
        ->name('subject-assignments');

    // روت متقدم: جلب مدرسين لدورة معينة (عبر المواد المخصصة)
    Route::get('/{batch}/instructors', [BatchSubjectsController::class, 'getInstructorsByBatch'])
        ->name('batch-instructors');

    // روت متقدم: جلب مواد لشعبة معينة (عبر الدورات التابعة للشعبة)
    Route::get('/branches/{branch}/subjects', [BatchSubjectsController::class, 'getSubjectsByBranch'])
        ->name('branch-subjects');

    // روت متقدم: جلب إحصائيات بسيطة لدورة (عدد المواد، عدد المدرسين)
    Route::get('/{batch}/stats', [BatchSubjectsController::class, 'getBatchStats'])
        ->name('batch-stats');

    Route::get('/summary', [BatchSubjectsController::class, 'getBatchSubjectsSummary'])->name('batch-subjects.summary');
});
