<?php

namespace Modules\Students\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentExamResultResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'exam_id'        => $this->exam_id,
            'student_id'     => $this->student_id,
            'obtained_marks' => $this->obtained_marks,
            'is_passed'      => $this->is_passed,
            'remarks'        => $this->remarks,                
            'subject_name'   => $this->exam?->batchSubject?->subject?->name, // اسم المادة
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
