<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Students\Models\Student;
use Modules\Attendances\Models\Attendance;

echo "--- START DIAGNOSTIC ---\n";

$ahmed = Student::all()->filter(function($s) {
    return str_contains($s->full_name, 'أحمد الخطيب') || str_contains($s->full_name, 'احمد الخطيب');
})->first();

if (!$ahmed) {
    echo "Student 'Ahmed Al-Khatib' not found by name search.\n";
    // Check most recent attendance to find ID
    $latestAtt = Attendance::latest()->first();
    if ($latestAtt) {
        $ahmed = $latestAtt->student;
        echo "Found Ahmed? from latest attendance: ID " . ($ahmed->id ?? 'None') . " Name: " . ($ahmed->full_name ?? 'N/A') . "\n";
    }
}

if ($ahmed) {
    echo "Student ID: " . $ahmed->id . "\n";
    echo "Student Branch ID: " . $ahmed->institute_branch_id . "\n";
    echo "Batches Ahmed is in:\n";
    foreach ($ahmed->batchStudents as $bs) {
        echo "- Batch ID: " . $bs->batch_id . " Name: " . ($bs->batch->name ?? 'N/A') . " (Hidden: " . ($bs->batch->is_hidden ? 'Yes':'No') . ", Archived: " . ($bs->batch->is_archived ? 'Yes':'No') . ")\n";
    }
    
    echo "Attendance records for this student:\n";
    $atts = Attendance::where('student_id', $ahmed->id)->get();
    foreach ($atts as $att) {
        echo "- Date: " . $att->attendance_date . " Status: " . $att->status . " Created: " . $att->created_at . "\n";
    }
} else {
    echo "No student found to track.\n";
}

echo "--- END DIAGNOSTIC ---\n";
