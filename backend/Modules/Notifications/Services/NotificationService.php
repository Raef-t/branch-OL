<?php

namespace Modules\Notifications\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Notifications\Models\Notification;
use Modules\NotificationAttachments\Models\NotificationAttachment;
use Modules\NotificationRecipients\Models\NotificationRecipient;
use Modules\Notifications\Events\NotificationCreated;

class NotificationService
{
    public function createNotification(array $data): Notification
    {
        return DB::transaction(function () use ($data) {

            // 1️⃣ إنشاء الإشعار
            $notification = Notification::create([
                'title'           => $data['title'],
                'body'            => $data['body'],
                'template_id'     => $data['template_id'] ?? null,
                'sender_id'       => $data['sender_id'] ?? null,
                'sender_type'     => $data['sender_type'] ?? null,
                'target_snapshot' => $data['target_snapshot'] ?? null,
            ]);

            // 2️⃣ المرفقات
            if (!empty($data['attachments'])) {
                foreach ($data['attachments'] as $file) {
                    $path = $file->store("notifications/{$notification->id}");

                    NotificationAttachment::create([
                        'notification_id' => $notification->id,
                        'file_name'       => $file->getClientOriginalName(),
                        'file_path'       => $path,
                        'mime_type'       => $file->getClientMimeType(),
                        'size'            => $file->getSize(),
                    ]);
                }
            }

            // 3️⃣ تحليل وتوسيع قائمة المستلمين
            $targetSnapshot = $data['target_snapshot'] ?? [];
            $type = $targetSnapshot['type'] ?? 'custom';
            $userIds = [];

            if ($type === 'custom') {
                $userIds = $data['user_ids'] ?? ($targetSnapshot['user_ids'] ?? []);
            } elseif ($type === 'all') {
                // جلب جميع مستخدمي الطلاب أو العائلات الذين لديهم طلاب (بشرط أن لا تكون شعبهم مخفية أو مؤرشفة)
                $userIds = \Modules\Users\Models\User::where(function ($query) {
                    $query->whereHas('student', function ($q) {
                        $q->where(function ($sub) {
                            $sub->whereDoesntHave('latestBatchStudent')
                                ->orWhereHas('latestBatchStudent.batch'); // السكوبات العالمية تتكفل بالباقي
                        });
                    })->orWhereHas('family', function ($q) {
                        $q->whereHas('students', function ($sub) {
                            $sub->whereDoesntHave('latestBatchStudent')
                                ->orWhereHas('latestBatchStudent.batch');
                        });
                    });
                })->pluck('id')->toArray();
            } elseif ($type === 'branch') {
                $branchId = $targetSnapshot['branch_id'] ?? null;
                if ($branchId) {
                    $userIds = \Modules\Users\Models\User::where(function ($query) use ($branchId) {
                        $query->whereHas('student', function ($q) use ($branchId) {
                            $q->where('institute_branch_id', $branchId)
                              ->where(function ($sub) {
                                $sub->whereDoesntHave('latestBatchStudent')
                                    ->orWhereHas('latestBatchStudent.batch');
                              });
                        })->orWhereHas('family', function ($q) use ($branchId) {
                            $q->whereHas('students', function ($sub) use ($branchId) {
                                $sub->where('institute_branch_id', $branchId)
                                    ->where(function ($s) {
                                        $s->whereDoesntHave('latestBatchStudent')
                                          ->orWhereHas('latestBatchStudent.batch');
                                    });
                            });
                        });
                    })->pluck('id')->toArray();
                }
            } elseif ($type === 'batch') {
                $batchId = $targetSnapshot['batch_id'] ?? null;
                if ($batchId) {
                    $userIds = \Modules\Users\Models\User::whereHas('student.batchStudents', function ($q) use ($batchId) {
                        $q->where('batch_id', $batchId);
                    })->orWhereHas('family.students.batchStudents', function ($q) use ($batchId) {
                        $q->where('batch_id', $batchId);
                    })->pluck('id')->toArray();
                }
            }

            if (!empty($userIds)) {
                $userIds = array_unique(array_filter($userIds));
                
                foreach ($userIds as $userId) {
                    NotificationRecipient::firstOrCreate([
                        'notification_id' => $notification->id,
                        'user_id'         => $userId,
                    ]);
                }

                Log::info('📦 تم إنشاء مستلمي الإشعار بنجاح', [
                    'notification_id' => $notification->id,
                    'type' => $type,
                    'recipients_count' => count($userIds),
                ]);
            }

            // 4️⃣ إطلاق الحدث
            event(new NotificationCreated($notification));

            return $notification->load([
                'attachments',
                'recipients',
                'template',
            ]);
        });
    }
}
