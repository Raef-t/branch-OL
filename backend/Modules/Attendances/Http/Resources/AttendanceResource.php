<?php

namespace Modules\Attendances\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'institute_branch_id' => $this->institute_branch_id,
            'student_id' => $this->student_id,
            'batch_id' => $this->batch_id,
            'attendance_date' => $this->attendance_date,
            'status' => $this->status,
            'recorded_by' => $this->recorded_by,
            'device_id' => $this->device_id,
            'recorded_at' => $this->recorded_at,
        ];
    }
}