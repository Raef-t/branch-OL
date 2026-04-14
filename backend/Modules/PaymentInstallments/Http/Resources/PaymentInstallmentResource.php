<?php

namespace Modules\PaymentInstallments\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentInstallmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'enrollment_contract_id' => $this->enrollment_contract_id,
            'student_id' => $this->enrollmentContract?->student_id,
            'installment_number' => $this->installment_number,
            'due_date' => $this->due_date,
            'planned_amount_usd' => $this->planned_amount_usd,
            'exchange_rate_at_due_date' => $this->exchange_rate_at_due_date,
            'planned_amount_syp' => $this->planned_amount_syp,
            'paid_amount_usd' => $this->paid_amount_usd,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}