<?php

namespace Modules\ExamResults\Http\Resources;

use OpenApi\Annotations as OA;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResultDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'exam_id' => $this->exam_id,
            'student_id' => $this->student_id,
            'student_name' => $this->student->fullName ?? null,
            'student_photo' => $this->student->profile_photo_url ?? null,
            'obtained_marks' => $this->obtained_marks,
            'is_passed' => $this->is_passed,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
