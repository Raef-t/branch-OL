<?php

namespace Modules\AuthorizedDevices\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthorizedDeviceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'device_id' => $this->device_id,
            'device_name' => $this->device_name,
            'is_active' => $this->is_active,
            'last_used_at' => $this->last_used_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}