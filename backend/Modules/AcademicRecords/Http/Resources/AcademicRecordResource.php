<?php

namespace Modules\AcademicRecords\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AcademicRecordResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'student_id'   => $this->student_id,
            'record_type'  => $this->record_type,
            'total_score'  => $this->total_score,
            'year'         => $this->year,
            'description'  => $this->description,
            'created_at'   => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'   => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}