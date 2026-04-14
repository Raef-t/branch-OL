<?php
use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\CheckInstallmentDelays;
use App\Jobs\CheckMonthlyInstallmentsJob;


Schedule::command('doorsessions:cleanup')->everyTenMinutes();

// أمر جاهز للإلهام
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Job: CheckMonthlyInstallmentsJob -> تشغيل من يوم 1 إلى يوم 5
Artisan::command('schedule:check-monthly-installments', function () {
    dispatch(new CheckMonthlyInstallmentsJob());
})->describe('Dispatch CheckMonthlyInstallmentsJob between 1 and 5 of the month')
  ->daily()
  ->when(fn () => now()->day >= 1 && now()->day <= 5);


// Job: CheckInstallmentDelays -> تشغيل يوم 6
Artisan::command('schedule:check-installment-delays', function() {
    dispatch(new CheckInstallmentDelays());
})->description('Dispatch CheckInstallmentDelays Job on the 6th day of the month (with catch-up logic)')
    ->dailyAt('00:00')  // يتحقق كل يوم الساعة 00:00
    ->when(function () {
            // التحقق: لو اليوم >=6 و المهمة ما نفذت في الشهر الحالي
            $task = \App\Models\ScheduledTask::firstOrCreate(
                ['task_name' => 'check-installment-delays'],
                ['last_run_at' => null]
            );

            $currentMonth = now()->startOfMonth();
            return now()->day >= 6 && (!$task->last_run_at || $task->last_run_at->lt($currentMonth));
    });
