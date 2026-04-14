<?php

namespace App\Providers;

use Modules\Shared\Observers\GenericFileObserver;
use Illuminate\Support\ServiceProvider;
use Modules\Attendances\Models\Attendance;
use App\Observers\AttendanceObserver;
use App\Observers\ExamResultObserver;
use Modules\Payments\Models\Payment;
use App\Observers\PaymentObserver;
use Modules\StudentExits\Models\StudentExitLog;
use App\Observers\StudentExitLogObserver;
use Modules\ExamResults\Models\ExamResult;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach (config('files.fileFieldsMap') as $modelClass => $fields) {
            $modelClass::observe(GenericFileObserver::class);
        }
        StudentExitLog::observe(StudentExitLogObserver::class);
        Attendance::observe(AttendanceObserver::class);

        Payment::observe(PaymentObserver::class); 
        ExamResult::observe(ExamResultObserver::class);
    }
}
