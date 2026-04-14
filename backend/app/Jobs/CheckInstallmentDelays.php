<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\EnrollmentContracts\Models\EnrollmentContract;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\ScheduledTask;

class CheckInstallmentDelays implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public function handle()
    {
        try {
            $today = Carbon::today();
            $monthStart = $today->copy()->startOfMonth();
            $limitDate = $monthStart->addDays(4); 

            $contracts = EnrollmentContract::with('paymentInstallments')
                ->where('is_active', true)
                ->get();

            $hasChanges = false;  // لتتبع إذا حصل تغيير

            foreach ($contracts as $contract) {
                if ($contract->discount_percentage <= 0) continue;

                $hasLate = $contract->paymentInstallments->contains(function ($inst) use ($limitDate) {
                    return Carbon::parse($inst->due_date)->lessThan($limitDate)
                           && $inst->status !== 'paid';
                });

                Log::info("Contract ID {$contract->id} has late installments: " . ($hasLate ? 'Yes' : 'No'));  // غيرت alert لـ info

                if ($hasLate) {
                    $this->removeDiscountAndRecalculate($contract);
                    $hasChanges = true;
                }
            }

            // تحديث السجل فقط إذا نفذت المهمة (حتى لو ما في تغييرات)
            if ($hasChanges) {  // أو اجعلها دائمًا، حسب احتياجك
                $task = ScheduledTask::updateOrCreate(
                    ['task_name' => 'check-installment-delays'],
                    ['last_run_at' => now()]
                );
                Log::info('CheckInstallmentDelays task executed and logged at: ' . now());
            }

        } catch (\Exception $e) {
            Log::error('Error in CheckInstallmentDelays: ' . $e->getMessage());
            throw $e;  // أعد رمي الخطأ عشان الـ Job يفشل ويُعاد
        }
    }

    private function removeDiscountAndRecalculate($contract)
    {
        $oldDiscount = $contract->discount_percentage;
        $totalAmount = $contract->total_amount_usd;

        // احسب مقدار الحسم القديم
        $discountAmount = $totalAmount * ($oldDiscount / 100);

        // إزالة الحسم من العقد
        $contract->discount_percentage = 0;
        $contract->final_amount_usd = $totalAmount;
        $contract->save();

        // الأقساط غير المدفوعة فقط
        $unpaidInstallments = $contract->paymentInstallments->filter(function ($inst) {
            return $inst->status !== 'paid';
        });

        $count = $unpaidInstallments->count();
        if ($count === 0) return;

        // الحصة الأساسية لكل قسط
        $share = floor(($discountAmount / $count) * 100) / 100; // تقريب لجزئين عشريين
        $totalDistributed = $share * $count;

        // احسب المتبقي (بسبب التقريب)
        $remainder = round($discountAmount - $totalDistributed, 2);

        // وزع الحسم
        foreach ($unpaidInstallments as $index => $inst) {
            $addAmount = $share;    

            // أضف الباقي لأول قسط (أو آخر، حسب ما تريد)
            if ($index === 0 && $remainder > 0) {
                $addAmount += $remainder;
            }

            $inst->planned_amount_usd += $addAmount;
            $inst->save();
        }

    }

}
