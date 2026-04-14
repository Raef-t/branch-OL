<?php

namespace Modules\DoorDevices\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DoorDeviceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'device_id' => $this->device_id,
            'name' => $this->name,
            'location' => $this->location,
            'is_active' => $this->is_active,
            'last_seen_at' => $this->last_seen_at,
            'api_key' => $this->api_key,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}