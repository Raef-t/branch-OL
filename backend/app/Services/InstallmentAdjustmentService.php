<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Modules\PaymentInstallments\Models\PaymentInstallment;
use Modules\Payments\Models\Payment;

class InstallmentAdjustmentService
{
    /**
     * تعديل الأقساط بناءً على فرق المبلغ في الدفعة.
     *
     * @param Payment $payment الدفعة المحدثة
     * @param float $difference الفرق في المبلغ (موجب أو سالب)
     * @throws Exception إذا فشل التعديل (مثل عدم توفر عقد أو فرق يتجاوز الحدود)
     */
    public function adjustForAmountDifference(Payment $payment, float $difference): void
    {
        $contract = $payment->enrollmentContract;

        if (!$contract) {
            throw new Exception('عقد التسجيل غير موجود');
        }

        DB::transaction(function () use ($contract, $difference) {

            $installments = PaymentInstallment::where('enrollment_contract_id', $contract->id)
                ->orderBy('installment_number')
                ->lockForUpdate()
                ->get();

            if (abs($difference) < 0.01) {
                return;
            }

            if ($difference > 0) {
                $this->distributePositiveDifference($installments, $difference);
            } else {
                $this->reverseNegativeDifference($installments, abs($difference), $contract);
            }

            // ⬅️ هنا فقط يتم تحديث العقد بناءً على الأقساط
            $contract->paid_amount_usd += $difference;
            $contract->save();
        });
    }

    /**
     * توزيع فرق موجب على الأقساط المعلقة.
     */
    private function distributePositiveDifference($installments, float $difference): void
    {
        // حساب إجمالي الأقساط المعلقة
        $totalRemaining = $installments
            ->filter(fn($i) => $i->status !== 'paid')
            ->sum(fn($i) => $i->planned_amount_usd - $i->paid_amount_usd);

        if ($difference > $totalRemaining) {
            throw new Exception('الفرق في المبلغ يتجاوز إجمالي الأقساط المعلقة');
        }

        $remainingDiff = $difference;

        foreach ($installments as $installment) {
            if ($installment->status === 'paid' || $remainingDiff <= 0) {
                continue;
            }

            $remainingToPay = $installment->planned_amount_usd - $installment->paid_amount_usd;
            $apply = min($remainingDiff, $remainingToPay);

            $installment->paid_amount_usd += $apply;
            $remainingDiff -= $apply;

            $installment->status = ($installment->paid_amount_usd >= $installment->planned_amount_usd) ? 'paid' : 'pending';
            $installment->save();
        }
    }


    /**
     * إرجاع فرق سالب من الأقساط المدفوعة (بدءًا من الأخيرة).
     */
    private function reverseNegativeDifference($installments, float $difference, $contract): void
    {
        // حساب إجمالي المدفوعات
        $totalPaid = $contract->paid_amount_usd;

        if ($difference > $totalPaid) {
            throw new Exception('الفرق في المبلغ يتجاوز إجمالي الأقساط المدفوعة');
        }

        $remainingDiff = $difference;
        $reversedInstallments = $installments->reverse();

        foreach ($reversedInstallments as $installment) {
            if ($remainingDiff <= 0) {
                break;
            }

            $take = min($installment->paid_amount_usd, $remainingDiff);
            $installment->paid_amount_usd -= $take;
            $remainingDiff -= $take;

            $installment->status = ($installment->paid_amount_usd >= $installment->planned_amount_usd) ? 'paid' : 'pending';
            $installment->save();
        }
    }
}