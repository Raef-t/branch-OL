<?php

namespace Modules\Notifications\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        // هذا المورد يستخدم بشكل رئيسي مع نموذج NotificationRecipient
        // لذا $this->resource هو مثيل من NotificationRecipient

        // الوصول إلى الإشعار
        $notification = $this->notification;

        // إذا لم نتمكن من الحصول على الإشعار
        if (!$notification) {
            return [
                'id' => null,
                'title' => 'غير متاح',
                'body' => 'غير متاح',
                'is_read' => !is_null($this->read_at ?? null),
                'created_at' => $this->created_at?->toDateTimeString(),
            ];
        }

        // التحقق من وجود المرفقات
        $attachments = [];
        if ($this->relationLoaded('notification') && $notification->relationLoaded('attachments')) {
            $attachments = $notification->attachments->map(function ($attachment) use ($request) {
                // إنشاء الرابط بشكل صحيح بدون //
                $baseUrl = config('app.url');
                $storagePath = 'storage/' . ltrim($attachment->file_path, '/');
                $fullUrl = rtrim($baseUrl, '/') . '/' . $storagePath;

                return [
                    'id' => $attachment->id,
                    'file_name' => $attachment->file_name,
                    'file_path' => $attachment->file_path,
                    'url' => $fullUrl,
                    // 'url_asset' => asset('storage/' . $attachment->file_path),
                    // 'url_storage' => Storage::url($attachment->file_path),
                    'mime_type' => $attachment->mime_type,
                    'size' => $attachment->size,
                    'size_formatted' => $this->formatBytes($attachment->size),
                ];
            })->toArray();
        }

        // التحقق من وجود القالب
        $template = null;
        if ($this->relationLoaded('notification') && $notification->relationLoaded('template')) {
            $template = $notification->template ? [
                'id' => $notification->template->id,
                'name' => $notification->template->name ?? null,
            ] : null;
        }

        // التحقق من عدد المستلمين
        $recipientsCount = 0;
        if ($this->relationLoaded('notification') && $notification->relationLoaded('recipients')) {
            $recipientsCount = $notification->recipients->count();
        }

        return [
            'reception_id' => $this->id,
            'notification_id' => $notification->id,
            'title' => $notification->title,
            'body' => $notification->body,

            'sender' => [
                'id' => $notification->sender_id,
                'type' => $notification->sender_type,
            ],

            'template' => $template,

            'target_snapshot' => $notification->target_snapshot,

            'attachments' => $attachments,

            'recipients_count' => $recipientsCount,

            'created_at' => $notification->created_at?->toDateTimeString(),
            'created_at_human' => $notification->created_at?->diffForHumans(),

            // معلومات من علاقة المستلم (NotificationRecipient)
            'read_at' => $this->read_at?->toDateTimeString(),
            'delivered_at' => $this->delivered_at?->toDateTimeString(),
            'is_read' => !is_null($this->read_at),
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
