<?php

namespace Modules\Notifications\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Notifications\Models\Notification;
use App\Services\FirebaseService; // نفترض أن لديك هذا السيرفس

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Notification $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function handle(FirebaseService $firebaseService): void
    {
        // استخرج المستلمين
        $recipients = $this->notification->recipients()->with('user.fcmTokens')->get();

        foreach ($recipients as $recipient) {
            $tokens = $recipient->user->fcmTokens->pluck('token')->filter()->values()->toArray();

            if (!empty($tokens)) {
                $firebaseService->sendToMultipleTokens(
                    $tokens,
                    $this->notification->title,
                    $this->notification->body,
                    [
                        'notification_id' => (string)$this->notification->id,
                        'type' => 'custom',
                    ]
                );
            }
        }
    }
}
