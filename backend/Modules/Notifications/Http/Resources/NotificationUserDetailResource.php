<?php

namespace Modules\Notifications\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class NotificationUserDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        if (!$this->relationLoaded('notification') || !$this->notification) {
            return [
                'error' => 'بيانات الإشعار غير متوفرة',
            ];
        }

        $notification = $this->notification;

        return [
            // بيانات المستلم (الكيان الرئيسي)
            'recipient' => [
                'id' => $this->id,
                'notification_id' => $this->notification_id,
                'received_at' => $this->created_at?->toDateTimeString(),
                'received_at_human' => $this->created_at?->diffForHumans(),
                'read_at' => $this->read_at?->toDateTimeString(),
                'read_at_human' => $this->read_at?->diffForHumans(),
                'delivered_at' => $this->delivered_at?->toDateTimeString(),
                'is_read' => !is_null($this->read_at),
                'status' => $this->read_at ? 'read' : ($this->delivered_at ? 'delivered' : 'pending'),
            ],

            // بيانات الإشعار الأساسية (بدون بيانات حساسة)
            'notification' => [
                'id' => $notification->id,
                'title' => $notification->title,
                'body' => $notification->body,
                'sender' => $this->formatSender($notification),
                'template' => $notification->template ? [
                    'id' => $notification->template->id,
                    'name' => $notification->template->name,
                ] : null,
                'attachments' => $notification->attachments->map(function ($attachment) {
                    // 🔧 الحل: استخدام ltrim لإزالة / الزائدة
                    $filePath = ltrim($attachment->file_path, '/');
                    $url = Storage::url($filePath);

                    // إزالة // المزدوج إذا وجد
                    $url = str_replace('//storage', '/storage', $url);

                    return [
                        'id' => $attachment->id,
                        'name' => $attachment->file_name,
                        'url' => $url,
                        'mime_type' => $attachment->mime_type,
                        'size' => $attachment->size,
                        'size_formatted' => $this->formatBytes($attachment->size),
                        'is_image' => in_array($attachment->mime_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']),
                    ];
                })->values(),
                'created_at' => $notification->created_at?->toDateTimeString(),
                'created_at_human' => $notification->created_at?->diffForHumans(),
            ],
        ];
    }

    private function formatSender($notification): array
    {
        $senderType = $notification->sender_type ?? 'system';
        $displayNameMap = [
            'admin' => 'الإدارة',
            'system' => 'النظام',
            'user' => 'مستخدم',
            'teacher' => 'المعلم',
            'employee' => 'الموظف',
        ];

        return [
            'type' => $senderType,
            'display_name' => $displayNameMap[$senderType] ?? 'غير معروف',
        ];
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = floor(log($bytes) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
