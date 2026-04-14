<?php

namespace Modules\Payments\Services;

use Modules\Payments\Models\Payment;
use Modules\EnrollmentContracts\Models\EnrollmentContract;
use Illuminate\Support\Facades\DB;
use Modules\Shared\Traits\SuccessResponseTrait;

class PaymentService
{
    use SuccessResponseTrait;

    /**
     * إنشاء الدفعة الأولى عند تسجيل العقد
     *
     * @param array $paymentData
     * @param EnrollmentContract $enrollmentContract
     * @return Payment|JsonResponse
     */
    public function createFirstPayment(array $paymentData, EnrollmentContract $enrollmentContract)
    {
        return DB::transaction(function () use ($paymentData, $enrollmentContract) {

            // =========================
            // 1. تحديد مبلغ الدفعة بالدولار
            // =========================
            $firstPaymentUsd = 0;

            if (!empty($paymentData['amount_usd'])) {
                $firstPaymentUsd = $paymentData['amount_usd'];
            } elseif (!empty($paymentData['amount_syp']) && !empty($paymentData['exchange_rate_at_payment'])) {
                $firstPaymentUsd = $paymentData['amount_syp'] / $paymentData['exchange_rate_at_payment'];
            }

            if ($firstPaymentUsd <= 0) {
                return $this->error('مبلغ الدفعة الأولى يجب أن يكون أكبر من صفر.', 422);
            }

            // =========================
            // 2. التأكد أن الدفعة لا تتجاوز المبلغ النهائي
            // =========================
            if ($firstPaymentUsd > $enrollmentContract->final_amount_usd) {
                return $this->error('الدفعة الأولى أكبر من المبلغ النهائي للعقد.', 422);
            }

            // =========================
            // 3. خصم الدفعة من المبلغ النهائي وتحديث العقد
            // =========================
            $remainingUsd = $enrollmentContract->final_amount_usd - $firstPaymentUsd;

            $enrollmentContract->final_amount_usd = $remainingUsd;
            $enrollmentContract->paid_amount_usd = $firstPaymentUsd;
            $enrollmentContract->save();

            // =========================
            // 4. إنشاء سجل الدفع في جدول payments
            // =========================
            $payment = Payment::create([
                'receipt_number' => $paymentData['receipt_number'] ?? null,
                'institute_branch_id' => $paymentData['institute_branch_id'] ?? $enrollmentContract->institute_branch_id,
                'enrollment_contract_id' => $enrollmentContract->id,
                'amount_usd' => $firstPaymentUsd,
                'amount_syp' => !empty($paymentData['exchange_rate_at_payment'])
                    ? $firstPaymentUsd * $paymentData['exchange_rate_at_payment']
                    : null,
                'exchange_rate_at_payment' => $paymentData['exchange_rate_at_payment'] ?? null,
                'currency' => !empty($paymentData['amount_usd']) ? 'USD' : 'SYP',
                'paid_date' => $paymentData['paid_date'] ?? now(),
                'description' => $paymentData['description'] ?? 'First payment at enrollment',
            ]);

            return $payment;
        });
    }
}
