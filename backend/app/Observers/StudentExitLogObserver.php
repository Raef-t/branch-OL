<?php

namespace App\Observers;

use Modules\StudentExits\Models\StudentExitLog;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;
use Modules\Notifications\Models\Notification;

class StudentExitLogObserver
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * يشغل عند إنشاء سجل خروج جديد
     */
    public function created(StudentExitLog $exit)
    {
        try {
            // تحميل العلاقات اللازمة
            $exit->load([
                'student.family.user.fcmTokens',
            ]);

            $student = $exit->student;
            $family  = $student?->family;
            $user    = $family?->user;

            if (!$student || !$family || !$user) {
                Log::warning('StudentExitLogObserver: بيانات ناقصة', [
                    'exit_id' => $exit->id,
                    'has_student' => (bool)$student,
                    'has_family' => (bool)$family,
                    'has_user' => (bool)$user,
                ]);
                return;
            }

            // إعداد بيانات الإشعار
            $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
            $exitTime    = $exit->exit_time ? $exit->exit_time->format('H:i') : 'غير محدد';

            $title = "خروج الطالب من المعهد";
            $body  = "قام الطالب {$studentName} بالخروج من المعهد عند الساعة {$exitTime}.";

            if ($exit->exit_type) {
                $body .= " (نوع الخروج: {$exit->exit_type})";
            }

            // [REFACTORED] Using NotificationService to centralize logic
            // New Implementation:
            $notificationData = [
                'title'       => $title,
                'body'        => $body,
                'sender_type' => 'employee',
                'sender_id'   => $exit->recorded_by,
                'user_ids'    => [$user->id],
            ];

            $notification = app(\Modules\Notifications\Services\NotificationService::class)->createNotification($notificationData);

            Log::info('StudentExitLogObserver: Notification created via Service', [
                'exit_id'         => $exit->id,
                'notification_id' => $notification->id,
                'user_id'         => $user->id,
            ]);

            /* [OLD IMPLEMENTATION - COMMENTED OUT FOR REFERENCE]
            // استخراج توكنات FCM
            $tokens = $user->fcmTokens
                ->pluck('token')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            $data = [
                'type'       => 'exit',
                'student_id' => (string)$student->id,
                'exit_id'    => (string)$exit->id,
            ];

            // إرسال الإشعار عبر Firebase
            if (!empty($tokens)) {
                $res = $this->firebase->sendToMultipleTokens($tokens, $title, $body, $data);
                Log::info('StudentExitLogObserver: إشعار FCM تم إرساله', [
                    'exit_id' => $exit->id,
                    'user_id' => $user->id,
                    'tokens_count' => count($tokens),
                    'response' => $res
                ]);
            } else {
                Log::info('StudentExitLogObserver: لا توجد توكنات FCM للمستخدم', ['user_id' => $user->id]);
            }

            // تحديد نوع المرسل
            $senderType = 'employee';
            $senderId   = $exit->recorded_by;

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

            Log::info('StudentExitLogObserver: الإشعار تم حفظه في قاعدة البيانات', [
                'exit_id'         => $exit->id,
                'notification_id' => $notification->id,
                'user_id'         => $user->id,
            ]);
            */

        } catch (\Throwable $e) {
            Log::error('StudentExitLogObserver: فشل إرسال أو حفظ إشعار الخروج', [
                'exit_id' => $exit->id ?? null,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
        }
    }
}
