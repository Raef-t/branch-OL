<?php

namespace App\Observers;

use Modules\Payments\Models\Payment;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;
use Modules\Notifications\Models\Notification;

class PaymentObserver
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * يُشغل فقط عند إضافة دفعة جديدة
     */
    public function created(Payment $payment)
    {
        Log::info('🔴 PaymentObserver::created - ENTRY POINT', ['payment_id' => $payment->id]);

        try {
            Log::info("PaymentObserver: triggered for payment {$payment->id}");

            // تحميل العلاقات الضرورية
            $payment->load([
                'enrollmentContract.student.family.user.fcmTokens'
            ]);

            $enrollment = $payment->enrollmentContract;
            if (!$enrollment) {
                Log::warning('PaymentObserver: No enrollment contract found', ['payment_id' => $payment->id]);
                return;
            }

            $student = $enrollment->student;
            $family  = $student?->family;
            $user    = $family?->user;

            if (!$student || !$family || !$user) {
                Log::warning('PaymentObserver: Missing related data', [
                    'payment_id' => $payment->id,
                    'student_exists' => (bool)$student,
                    'family_exists' => (bool)$family,
                    'user_exists' => (bool)$user,
                ]);
                return;
            }

            // [REFACTORED] Using NotificationService to centralize logic
            // New Implementation:
            $notificationData = [
                'title'       => $title,
                'body'        => $body,
                'sender_type' => 'employee',
                'sender_id'   => request()->user()?->id,
                'user_ids'    => [$user->id],
            ];
            
            $notification = app(\Modules\Notifications\Services\NotificationService::class)->createNotification($notificationData);

            Log::info('PaymentObserver: Notification created via Service', [
                'payment_id'      => $payment->id,
                'notification_id' => $notification->id,
                'user_id'         => $user->id,
            ]);

            /* [OLD IMPLEMENTATION - COMMENTED OUT FOR REFERENCE]
            // استخراج FCM tokens مع تصفية الفارغة
            $tokens = $user->fcmTokens
                ->pluck('token')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            $data = [
                'type' => 'payment',
                'student_id' => (string)$student->id,
                'payment_id' => (string)$payment->id,
            ];

            // إرسال الإشعار عبر Firebase
            if (!empty($tokens)) {
                $this->firebaseService->sendToMultipleTokens($tokens, $title, $body, $data);
                Log::info('PaymentObserver: FCM notification sent', [
                    'payment_id' => $payment->id,
                    'user_id'    => $user->id,
                    'tokens_count' => count($tokens)
                ]);
            } else {
                Log::info('PaymentObserver: No FCM tokens found', ['user_id' => $user->id]);
            }

            // تحديد نوع المرسل (الدفع دائماً يتم بواسطة موظف)
            $senderType = 'employee';
            $senderId   = request()->user()?->id;

            Log::info('PaymentObserver: sender debug', [
                'sender_id' => $senderId,
                'request_user' => request()->user()?->id,
            ]);

            // حفظ الإشعار في جدول notifications
            $notification = Notification::create([
                'title'       => $title,
                'body'        => $body,
                'sender_type' => $senderType,
                'sender_id'   => $senderId,
            ]);

            // ربط الإشعار بالمستخدم (المستلم) في جدول notification_recipients
            \Modules\NotificationRecipients\Models\NotificationRecipient::create([
                'notification_id' => $notification->id,
                'user_id'         => $user->id,
                'delivered_at'    => now(),
            ]);

            Log::info('PaymentObserver: Notification saved in database', [
                'payment_id'      => $payment->id,
                'notification_id' => $notification->id,
                'user_id'         => $user->id,
            ]);
            */

        } catch (\Throwable $e) {
            Log::error('PaymentObserver: Failed to send or save payment notification', [
                'payment_id' => $payment->id ?? null,
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString()
            ]);
        }
    }
}
