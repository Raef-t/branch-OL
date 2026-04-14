<?php

namespace Modules\Notifications\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class UserNotificationListResource extends JsonResource
{
    public function toArray($request): array
    {
        $notification = $this->notification;

        return [
            // هذا هو المفتاح الأهم
            'recipient_id' => $this->id,

            'notification_id' => $notification->id,

            'title' => $notification->title,

            // معاينة قصيرة فقط
            'preview' => Str::limit($notification->body, 120),

            'sender' => [
                'id' => $notification->sender_id,
                'type' => $notification->sender_type,
            ],

            // حالة المستلم فقط
            'is_read' => !is_null($this->read_at),
            'read_at' => $this->read_at,
            'delivered_at' => $this->delivered_at,

            // معلومات خفيفة
            'has_attachments' => $notification->attachments->isNotEmpty(),
            'attachments_count' => $notification->attachments->count(),

            'created_at' => $this->created_at,
            'created_at_human' => $this->created_at->diffForHumans(),
        ];
    }
}
