<?php

namespace Modules\Notifications\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Notifications\Models\Notification;

class NotificationCreated
{
    use Dispatchable, SerializesModels;

    public Notification $notification;

    /**
     * فقط نمرر الإشعار، بدون أي منطق
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }
}
