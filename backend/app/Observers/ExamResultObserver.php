<?php

namespace App\Observers;

use Modules\Students\Models\Student;
use Modules\ExamResults\Models\ExamResult; // افتراضي - غيّر المسار إذا كان مختلفاً
use Modules\Notifications\Models\Notification;
use Modules\FcmTokens\Models\FcmToken;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;

class ExamResultObserver
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Handle the "created" event (عند إنشاء نتيجة امتحان جديدة)
     */
    public function created(ExamResult $examResult)
    {
        try {
            Log::info("ExamResultObserver: triggered for exam result {$examResult->id}");

            // تحميل العلاقات المهمة مرة واحدة
            $examResult->load([
                'student.user',
                'student.family.user',
            ]);

            $student = $examResult->student;

            if (!$student) {
                Log::warning('ExamResultObserver: No student found', ['exam_result_id' => $examResult->id]);
                return;
            }

            // عنوان ونص الإشعار الموحد
            $title = 'نتيجة امتحان جديدة';
            $body  = "تم صدور نتيجة الطالب {$student->first_name} بمجموع {$examResult->obtained_marks} "
                   . "في الامتحان رقم {$examResult->exam_id}.";

            // جمع المستلمين (ممكن يكون أكثر من مستخدم)
            $recipients = [];

            // 1. حساب المستخدم المرتبط مباشرة بالطالب (إذا وجد)
            if ($student->user) {
                $recipients[$student->user->id] = $student->user;
            }

            // 2. حساب ولي الأمر / العائلة
            if ($student->family?->user) {
                $recipients[$student->family->user->id] = $student->family->user;
            }

            if (empty($recipients)) {
                Log::info('ExamResultObserver: No recipients found to notify', [
                    'exam_result_id' => $examResult->id,
                    'student_id'     => $student->id
                ]);
                return;
            }

            // البيانات الإضافية التي ترسل مع الإشعار
            $data = [
                'student_id' => (string) $student->id,
                'exam_id'    => (string) $examResult->exam_id,
                'type'       => 'exam_result',
            ];

            // [REFACTORED] Using NotificationService to centralize logic
            // New Implementation:
            $recipientUserIds = array_keys($recipients);

            if (!empty($recipientUserIds)) {
                $notificationData = [
                    'title'       => $title,
                    'body'        => $body,
                    'sender_type' => 'employee',
                    'sender_id'   => request()->user()?->id,
                    'user_ids'    => $recipientUserIds, // Send to all recipients in one go
                ];

                $notification = app(\Modules\Notifications\Services\NotificationService::class)->createNotification($notificationData);

                Log::info('ExamResultObserver: Notification created via Service', [
                    'exam_result_id'  => $examResult->id,
                    'notification_id' => $notification->id,
                    'recipient_count' => count($recipientUserIds),
                ]);
            }

            /* [OLD IMPLEMENTATION - COMMENTED OUT FOR REFERENCE]
            // معالجة كل مستلم على حدة
            foreach ($recipients as $userId => $user) {
                // جلب توكنات FCM الخاصة بهذا المستخدم
                $tokens = FcmToken::where('user_id', $userId)
                    ->pluck('token')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();

                if (empty($tokens)) {
                    Log::info('ExamResultObserver: No FCM tokens for user', ['user_id' => $userId]);
                    continue;
                }

                // تحديد نوع المرسل (النتيجة تدخل بواسطة موظف/مدرس)
                $senderType = 'employee';
                $senderId   = request()->user()?->id;

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
                    'user_id'         => $userId,
                    'delivered_at'    => now(),
                ]);

                // إرسال الإشعار عبر Firebase
                $this->firebase->sendToMultipleTokens($tokens, $title, $body, $data);

                Log::info('ExamResultObserver: Notification sent & saved', [
                    'exam_result_id'  => $examResult->id,
                    'notification_id' => $notification->id,
                    'user_id'         => $userId,
                    'tokens_count'    => count($tokens),
                ]);
            }
            */

        } catch (\Throwable $e) {
            Log::error('ExamResultObserver: Failed to process exam result notification', [
                'exam_result_id' => $examResult->id ?? null,
                'error'          => $e->getMessage(),
                'trace'          => $e->getTraceAsString(),
            ]);
        }
    }
}