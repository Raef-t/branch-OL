<?php

namespace App\Services;

use App\Services\FirebaseService;
use Modules\Notifications\Models\Notification;
use Modules\FcmTokens\Models\FcmToken;
use Modules\Students\Models\Student;

class NotificationService
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function sendExamResultNotification(Student $student, $examResult)
    {
        // عنوان ونص الإشعار الموحد
        $title = 'نتيجة امتحان جديدة';
        $body = "تم صدور نتيجة الطالب {$student->first_name} بمجموع {$examResult->obtained_marks} "
              . "في الامتحان رقم {$examResult->exam_id}.";

        // جمع المستخدمين المستهدفين
        $recipients = [];

        // 1. user المرتبط بالطالب
        if ($student->user) {
            $recipients[$student->user->id] = $student->user;
        }

        // 2. user المرتبط بالعائلة
        if ($student->family?->user) {
            $recipients[$student->family->user->id] = $student->family->user;
        }

        // إذا ما في ولا مستخدم مرتبط، نوقف
        if (empty($recipients)) {
            return;
        }

        // إرسال الإشعار لكل مستخدم له FCM Tokens
        foreach ($recipients as $userId => $user) {
            $tokens = FcmToken::where('user_id', $userId)->pluck('token')->toArray();

            if (empty($tokens)) {
                continue;
            }

            // إنشاء سجل إشعار في قاعدة البيانات
            Notification::create([
                'title' => $title,
                'body' => $body,
                'type' => 'in_app',
                'target_type' => 'student',
                'target_id' => $student->id,
                'status' => 'sent',
                'sent_at' => now(),
                'user_id' => $userId, // إضافة user_id لتوثيق الجهة المستلمة
            ]);

            // إرسال إلى Firebase
            $this->firebase->sendToMultipleTokens($tokens, $title, $body, [
                'student_id' => (string) $student->id,
                'exam_id' => (string) $examResult->exam_id,
            ]);
        }

        return true;
    }
}
