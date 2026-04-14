<?php

namespace Modules\Students\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentFinancialSummaryResource extends JsonResource
{
    public function toArray($request)
    {
        $contract = $this->latestActiveEnrollmentContract;

        if (!$contract) {
            return [
                'student_id' => $this->id,
                'full_name' => $this->fullName,
                'message' => 'No active enrollment contract found.'
            ];
        }

        $totalAmount = $contract->total_amount_usd;
        $paidAmount  = $contract->paid_amount_usd;
        $remainingAmount = max($totalAmount - $paidAmount, 0);

        return [
            'student_id' => $this->id,
            'full_name' => $this->fullName,
            'enrollment_contract' => [
                'contract_id' => $contract->id,
                'total_amount_usd' => $totalAmount,
                'paid_amount_usd' => $paidAmount,
                'remaining_amount_usd' => $remainingAmount,
                'discount_percentage' => $contract->discount_percentage,
                'discount_amount' => $contract->discount_amount,
            ],
            'payments' => $contract->payments->map(function($payment){
                return [
                    'id' => $payment->id,
                    'receipt_number' => $payment->receipt_number,
                    'amount_usd' => $payment->amount_usd,
                    'paid_date' => $payment->paid_date,
                    'note' => $payment->note,
                ];
            }),
            'pending_installments' => $contract->paymentInstallments->map(function($installment){
                $remaining = max($installment->planned_amount_usd - $installment->paid_amount_usd, 0);
                return [
                    'id' => $installment->id,
                    'installment_number' => $installment->installment_number,
                    'due_date' => $installment->due_date,
                    'planned_amount_usd' => $installment->planned_amount_usd,
                    'paid_amount_usd' => $installment->paid_amount_usd,
                    'remaining_amount_usd' => $remaining,
                    'status' => $installment->status,
                ];
            }),
        ];
    }
}
