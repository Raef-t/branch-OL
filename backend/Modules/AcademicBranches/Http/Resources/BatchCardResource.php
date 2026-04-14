<?php

namespace Modules\AcademicBranches\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $classRoom = $this->classRoom;
        $studentsCount = $this->batchStudents?->count() ?? 0;

        $supervisor = $this->batchEmployees
            ?->where('role', 'supervisor')
            ->first();

        $employee = $supervisor?->employee;

        return [
            'batch_id'   => $this->id,
            'batch_name' => $this->name,

            'class_room_name' => $classRoom?->name,
            'is_classroom_full' => $this->is_completed,

            'start_date' => $this->start_date,

            'supervisor' => $employee ? [
                'name'  => $employee->full_name,
                'photo' => $employee->photo_url,
            ] : null,

            'students_count' => $studentsCount,

            'present_students' => $this->present_students ?? 0,
            'attendance_percentage' => $this->attendance_percentage ?? null,
        ];
    }
}
