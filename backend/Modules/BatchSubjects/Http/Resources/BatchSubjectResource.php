<?php

namespace Modules\BatchSubjects\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BatchSubjectResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'batch' => [
                'id' => $this->batch->id,
                'name' => $this->batch->name,
            ],
            'subject' => [
                'id' => $this->instructorSubject->subject->id ?? null,
                'name' => $this->instructorSubject->subject->name ?? null,
            ],
            'instructor' => [
                'id' => $this->instructorSubject->instructor->id ?? null,
                'name' => $this->instructorSubject->instructor->name ?? null,
            ],
            'notes' => $this->notes,
            'assigned_by' => $this->assigned_by,
            'assignment_date' => $this->assignment_date,
            'is_active' => $this->is_active,
        ];
    }
}
