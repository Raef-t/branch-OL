<?php
namespace Modules\AcademicBranches\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcademicBranchStatsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $students = $this->batches
            ->flatMap(fn ($batch) => $batch->batchStudents)
            ->pluck('student');

        return [
            'academic_branch_id'   => $this->id,
            'academic_branch_name' => $this->name,

            'students_count' => $students->unique('id')->count(),

            'male_students_count' => $students
                ->where('gender', 'male')
                ->unique('id')
                ->count(),

            'female_students_count' => $students
                ->where('gender', 'female')
                ->unique('id')
                ->count(),

            'batches_count' => $this->batches->count(),

            // نمرر batches مع نسبة الحضور
            'batches' => BatchCardResource::collection($this->batches),
        ];
    }
}
