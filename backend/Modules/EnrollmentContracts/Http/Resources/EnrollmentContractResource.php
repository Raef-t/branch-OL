<?php

namespace Modules\EnrollmentContracts\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentContractResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'total_amount_usd' => $this->total_amount_usd,
            'discount_percentage' => $this->discount_percentage,
            'discount_amount' => $this->discount_amount,
            'final_amount_usd' => $this->final_amount_usd,
            'paid_amount_usd' => $this->paid_amount_usd,    
            'exchange_rate_at_enrollment' => $this->exchange_rate_at_enrollment,
            'final_amount_syp' => $this->final_amount_syp,
            'agreed_at' => $this->agreed_at,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'mode' => $this->mode,
            'installments_count' => $this->installments_count,
            'installments_start_date' => $this->installments_start_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}