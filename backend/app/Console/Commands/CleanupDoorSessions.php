<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\DoorSessions\Models\DoorSession;

class CleanupDoorSessions extends Command
{
    protected $signature = 'doorsessions:cleanup';

    protected $description = 'حذف جلسات الباب المنتهية وغير المستخدمة';

    public function handle()
    {
        $count = DoorSession::where('is_used', false)
            ->where('expires_at', '<', now())
            ->delete();

        $this->info("Deleted {$count} expired unused door sessions.");

        return 0;
    }
}
