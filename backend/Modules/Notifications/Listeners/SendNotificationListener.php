<?php

namespace Modules\Notifications\Listeners;

use Modules\Notifications\Events\NotificationCreated;
use Modules\Notifications\Models\NotificationRecipient;
use App\Services\FirebaseService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Handle the event.
     */
    public function handle(NotificationCreated $event): void
    {
        Log::info('🔔 [Listener] بدء معالجة حدث إنشاء إشعار جديد', [
            'notification_id' => $event->notification->id,
            'title' => $event->notification->title,
            'created_at' => $event->notification->created_at
        ]);

        $notification = $event->notification;

        // جلب جميع المستلمين مع توكناتهم
        $recipients = $notification->recipients()->with('user.fcmTokens')->get();

        Log::info('👥 [Listener] عدد المستلمين المحددون', [
            'recipients_count' => $recipients->count(),
            'notification_id' => $notification->id
        ]);

        $totalSuccess = 0;
        $totalFailures = 0;

        foreach ($recipients as $recipient) {
            $user = $recipient->user;

            if (!$user) {
                Log::warning("⚠️ [Listener] المستلم غير موجود (مستخدم محذوف)", [
                    'recipient_id' => $recipient->id,
                    'notification_id' => $notification->id
                ]);
                $totalFailures++;
                continue;
            }

            Log::debug('🔍 [Listener] معالجة مستلم', [
                'user_id' => $user->id,
                'notification_id' => $notification->id,
                'recipient_id' => $recipient->id
            ]);

            // استخراج وتنقية توكنات FCM
            $tokens = $user->fcmTokens
                ->pluck('token')
                ->filter(fn($token) => is_string($token) && trim($token) !== '')
                ->map(fn($token) => (string) $token)
                ->values()
                ->toArray();

            // 🔍 تسجيل معلومات التوكنات قبل الإرسال
            Log::debug('📊 [Listener] معلومات توكنات المستخدم', [
                'user_id' => $user->id,
                'total_fcm_tokens' => $user->fcmTokens->count(),
                'valid_tokens_count' => count($tokens),
                'token_types' => array_map('gettype', $user->fcmTokens->pluck('token')->toArray()),
                'first_token_sample' => !empty($tokens) ? substr($tokens[0], 0, 15) . '...' : null
            ]);

            if (empty($tokens)) {
                Log::info("ℹ️ [Listener] لا توجد توكنات FCM صالحة للمستخدم", [
                    'user_id' => $user->id,
                    'notification_id' => $notification->id
                ]);
                $totalFailures++;
                continue;
            }

            // إعداد بيانات الإشعار
            $title = $notification->title;
            $body = $notification->body;

            $data = [
                'notification_id' => (string) $notification->id,
                'type' => $notification->template_id ? 'template' : 'custom',
                'created_at' => $notification->created_at->toIso8601String(),
            ];

            // إذا كان هناك مرفقات يمكن إضافة رابط لكل مرفق
            if ($notification->attachments->isNotEmpty()) {
                $data['attachments'] = $notification->attachments->map(fn($a) => [
                    'file_name' => $a->file_name,
                    'file_path' => $a->file_path,
                    'mime_type' => $a->mime_type,
                    'size' => $a->size,
                ])->toArray();
            }

            try {
                Log::info('📤 [Listener] بدء إرسال إشعار للمستخدم', [
                    'user_id' => $user->id,
                    'notification_id' => $notification->id,
                    'tokens_count' => count($tokens),
                    'title' => $title,
                    'body_sample' => substr($body, 0, 30) . '...'
                ]);

                $result = $this->firebaseService->sendToMultipleTokens(
                    $tokens,
                    $title,
                    $body,
                    $data
                );

                if (isset($result['success']) && $result['success'] === true && ($result['success_count'] ?? 0) > 0) {
                    Log::info('✅ [Listener] تم إرسال الإشعار بنجاح', [
                        'notification_id' => $notification->id,
                        'user_id' => $user->id,
                        'success_count' => $result['success_count'],
                        'failure_count' => $result['failure_count'] ?? 0,
                        'total_tokens' => count($tokens)
                    ]);

                    // ✅ تحديث حالة التسليم فقط عند النجاح
                    $recipient->update(['delivered_at' => now()]);
                    $totalSuccess++;
                } else {
                    Log::error('❌ [Listener] فشل إرسال الإشعار', [
                        'notification_id' => $notification->id,
                        'user_id' => $user->id,
                        'error' => $result['error'] ?? 'خطأ غير معروف',
                        'success_count' => $result['success_count'] ?? 0,
                        'failure_count' => $result['failure_count'] ?? count($tokens)
                    ]);
                    $totalFailures++;
                }
            } catch (\Throwable $e) {
                Log::error('🔥 [Listener] استثناء غير متوقع أثناء إرسال الإشعار', [
                    'notification_id' => $notification->id,
                    'user_id' => $user->id,
                    'error_message' => $e->getMessage(),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine(),
                    'error_trace' => substr($e->getTraceAsString(), 0, 300)
                ]);
                $totalFailures++;
            }
        }

        // 📊 تقرير نهائي
        Log::info('📈 [Listener] تقرير إرسال الإشعارات النهائي', [
            'notification_id' => $notification->id,
            'total_recipients' => $recipients->count(),
            'successful_deliveries' => $totalSuccess,
            'failed_deliveries' => $totalFailures,
            'success_rate' => $recipients->count() > 0
                ? round(($totalSuccess / $recipients->count()) * 100, 2) . '%'
                : '0%'
        ]);
    }
}
