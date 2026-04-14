<?php

namespace App\Observers;

use Modules\Attendances\Models\Attendance;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;
use Modules\Notifications\Models\Notification;

class AttendanceObserver
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Triggered only after a new attendance record is created
     */
    public function created(Attendance $attendance)
    {
        try {
            // Load required relations to reduce queries
            $attendance->load([
                'student.family.user.fcmTokens',
                'batch',
            ]);

            $student = $attendance->student;
            $family  = $student?->family;
            $user    = $family?->user;

            if (!$student || !$family || !$user) {
                Log::warning('AttendanceObserver: Missing related data', [
                    'attendance_id' => $attendance->id,
                    'student_exists' => (bool)$student,
                    'family_exists' => (bool)$family,
                    'user_exists' => (bool)$user,
                ]);
                return;
            }

            // Build notification content
            $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
            $batchName   = $attendance->batch?->name ?? 'غير محدد';

            $title = "تسجيل حضور الطالب";
            $body  = "تم تسجيل حضور الطالب {$studentName} في الشعبة {$batchName} بتاريخ {$attendance->attendance_date}";

            // [REFACTORED] Using NotificationService to centralize logic
            // New Implementation:
            $notificationData = [
                'title'       => $title,
                'body'        => $body,
                'sender_type' => $attendance->device_id ? 'system' : 'employee',
                'sender_id'   => $attendance->device_id ? null : $attendance->recorded_by,
                'user_ids'    => [$user->id],
            ];

            $notification = app(\Modules\Notifications\Services\NotificationService::class)->createNotification($notificationData);

            Log::info('AttendanceObserver: Notification created via Service', [
                'attendance_id'   => $attendance->id,
                'notification_id' => $notification->id,
                'user_id'         => $user->id,
            ]);

            /* [OLD IMPLEMENTATION - COMMENTED OUT FOR REFERENCE]
            // Extract FCM tokens
            $tokens = $user->fcmTokens
                ->pluck('token')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            // Build notification content
            $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
            $batchName   = $attendance->batch?->name ?? 'غير محدد';

            $title = "تسجيل حضور الطالب";
            $body  = "تم تسجيل حضور الطالب {$studentName} في الشعبة {$batchName} بتاريخ {$attendance->attendance_date}";

            $data = [
                'type'           => 'attendance',
                'student_id'     => (string)$student->id,
                'attendance_id'  => (string)$attendance->id,
            ];

            // Send notification via Firebase
            if (!empty($tokens)) {
                $result = $this->firebaseService->sendToMultipleTokens(
                    $tokens,
                    $title,
                    $body,
                    $data
                );

                Log::info('AttendanceObserver: Notification sent via FCM', [
                    'attendance_id' => $attendance->id,
                    'student_id'    => $student->id,
                    'user_id'       => $user->id,
                    'tokens_count'  => count($tokens),
                    'result'        => $result,
                ]);
            } else {
                Log::info('AttendanceObserver: No FCM tokens found for user', ['user_id' => $user->id]);
            }

            // 1. Determine sender type based on how attendance was recorded
            $senderType = $attendance->device_id ? 'system' : 'employee';
            $senderId   = $attendance->device_id ? null : $attendance->recorded_by;

            // 2. Create the notification content (Message)
            $notification = Notification::create([
                'title'       => $title,
                'body'        => $body,
                'sender_type' => $senderType,
                'sender_id'   => $senderId,
            ]);

            // 2. Link the notification to the user (Recipient)
            \Modules\NotificationRecipients\Models\NotificationRecipient::create([
                'notification_id' => $notification->id,
                'user_id'         => $user->id,
                'delivered_at'    => now(), // We just sent it via FCM
                // 'read_at' => null, // Initially unread
            ]);

            Log::info('AttendanceObserver: Notification saved in database', [
                'attendance_id'   => $attendance->id,
                'notification_id' => $notification->id,
                'user_id'         => $user->id,
            ]);
            */

        } catch (\Throwable $e) {
            Log::error('AttendanceObserver: Failed to send or save attendance notification', [
                'attendance_id' => $attendance->id,
                'error'         => $e->getMessage(),
                'trace'         => $e->getTraceAsString(),
            ]);
        }
    }
}
