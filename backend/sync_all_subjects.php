<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Batches\Models\Batch;
use Modules\BatchSubjects\Models\BatchSubject;
use Carbon\Carbon;

$batches = Batch::all();
echo "Syncing " . $batches->count() . " batches...\n";

foreach ($batches as $batch) {
    if (!$batch->academic_branch_id) {
        echo "Batch {$batch->id} ({$batch->name}) has no academic branch. Skipping.\n";
        continue;
    }

    $branchSubjects = $batch->academicBranch->subjects;
    $count = 0;

    foreach ($branchSubjects as $s) {
        // Check if already assigned
        $exists = BatchSubject::where('batch_id', $batch->id)
            ->where('subject_id', $s->id)
            ->exists();

        if (!$exists) {
            BatchSubject::create([
                'batch_id' => $batch->id,
                'subject_id' => $s->id,
                'assignment_date' => Carbon::now(),
                'is_active' => true,
            ]);
            $count++;
        }
    }

    echo "Batch {$batch->id} ({$batch->name}): Added {$count} subjects.\n";
}

echo "Sync completed.\n";
