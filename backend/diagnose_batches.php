<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Batches\Models\Batch;
use Modules\AcademicBranches\Models\AcademicBranch;

echo "--- BATCHES ---\n";
Batch::withCount('batchSubjects')->get()->each(function($b) {
    echo "ID: {$b->id} | Name: {$b->name} | AcademicBranchID: {$b->academic_branch_id} | Subjects: {$b->batch_subjects_count}\n";
});

echo "\n--- ACADEMIC BRANCHES ---\n";
AcademicBranch::withCount('subjects')->get()->each(function($ab) {
    echo "ID: {$ab->id} | Name: {$ab->name} | Subjects in Branch: {$ab->subjects_count}\n";
});
