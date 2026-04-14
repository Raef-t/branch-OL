<?php

namespace Modules\Payments\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentEditRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'payment_id' => $this->payment_id, 
            'requester_id' => $this->requester_id, 
            'original_data' => $this->original_data,
            'proposed_changes' => $this->proposed_changes,
            'reason' => $this->reason ?? null, 
            'status' => $this->status ?? 'pending', 
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}