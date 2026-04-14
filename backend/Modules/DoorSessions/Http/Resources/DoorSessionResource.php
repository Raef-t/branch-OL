<?php

namespace Modules\DoorSessions\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DoorSessionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'device_id' => $this->device_id,
            'session_token' => $this->session_token,
            'expires_at' => $this->expires_at,
            'is_used' => $this->is_used,
            'student_id' => $this->student_id,
            'used_at' => $this->used_at,
            'created_at' => $this->created_at,
        ];
    }
}