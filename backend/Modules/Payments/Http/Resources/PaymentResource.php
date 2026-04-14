<?php

namespace Modules\Payments\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'receipt_number' => $this->receipt_number,
            'institute_branch_id' => $this->institute_branch_id,
            'student_id' => $this->enrollmentContract->student_id,
            'enrollment_contract_id' => $this->enrollment_contract_id,
            'amount_usd' => $this->amount_usd,
            'amount_syp' => $this->amount_syp,
            'exchange_rate_at_payment' => $this->exchange_rate_at_payment,
            'currency' => $this->currency,  
            'due_date' => $this->due_date,
            'paid_date' => $this->paid_date,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}