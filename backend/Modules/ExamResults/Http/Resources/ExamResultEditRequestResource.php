<?php

namespace Modules\ExamResults\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamResultEditRequestResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'exam_result_id' => $this->exam_result_id, 
            'requester_id' => $this->requester_id, 
            'original_data' => $this->original_data,
            'proposed_changes' => $this->proposed_changes,
            'reason' => $this->reason ?? null, 
            'status' => $this->status ?? 'pending', 
            'type' => $this->type ?? 'update',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}