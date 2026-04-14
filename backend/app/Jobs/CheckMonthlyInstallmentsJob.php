<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\PaymentInstallments\Models\PaymentInstallment;
use Modules\Notifications\Models\Notification;
use Carbon\Carbon;
use App\Services\FirebaseService;
use Modules\FcmTokens\Models\FcmToken;

class CheckMonthlyInstallmentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $firebaseService;

    public function __construct()
    {
        $this->firebaseService = app(FirebaseService::class);
    }

    public function handle(): void
    {
        try {
            $today = Carbon::now();
            $startOfMonth = $today->copy()->startOfMonth();
            $endOfMonth = $today->copy()->endOfMonth();
            // نجلب كل الأقساط المستحقة خلال هذا الشهر ولم تُدفع بعد
            $installments = PaymentInstallment::whereBetween('due_date', [$startOfMonth, $endOfMonth])
                ->where('status', '!=', 'paid')
                ->get();

            Log::info('Checking monthly installments...', ['count' => $installments->count()]);

            foreach ($installments as $installment) {
                $student = $installment->enrollmentContract->student;
                $familyUser = $student?->family?->user;
            
                if (!$familyUser) {
                    continue;
                }

                $title = "تنبيه بدفعة مستحقة";
                $body = "يوجد دفعة مستحقة لهذا الشهر للطالب {$student->first_name} {$student->last_name}.";
                // إرسال الإشعارات عبر FirebaseService
                $tokens = FcmToken::where('user_id', $familyUser->id)
                ->pluck('token')
                ->toArray();
            
                if (!empty($tokens)) {
                    $this->firebaseService->sendToMultipleTokens(
                        $tokens,
                        $title,
                        $body,
                        [
                            'student_id' => $student->id,
                            'installment_id' => $installment->id,
                            'type' => 'installment_reminder',
                        ]
                    );
                }

                // تسجيل الإشعار في قاعدة البيانات
                Notification::create([
                    'title' => $title,
                    'body' => $body,
                    'type' => 'in_app',
                    'target_type' => 'family',
                    'target_id' => $familyUser->id,
                    'status' => 'sent',
                    'sent_at' => now(),
                    'user_id' => $familyUser->id,
                ]);
            }

            Log::info('Monthly installment check completed successfully.');
        } catch (\Throwable $e) {
            Log::error('Error in CheckMonthlyInstallmentsJob: ' . $e->getMessage());
        }
    }
}
