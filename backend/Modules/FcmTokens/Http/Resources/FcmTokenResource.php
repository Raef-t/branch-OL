<?php

namespace Modules\FcmTokens\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FcmTokenResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'token' => $this->token,
            'user_id' => $this->user_id,
            'device_info' => $this->device_info,
            'last_seen' => $this->last_seen,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}