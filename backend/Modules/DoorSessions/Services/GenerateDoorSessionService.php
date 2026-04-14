<?php

namespace Modules\DoorSessions\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Modules\DoorDevices\Models\DoorDevice;
use Modules\DoorSessions\Models\DoorSession;

class GenerateDoorSessionService
{
    /**
     * إنشاء جلسة تسجيل دخول جديدة عبر QR لجهاز معين.
     */
    public function createForDevice(DoorDevice $device): DoorSession
    {
        // توليد توكن فريد (يُستخدم في QR)
        $token = Str::uuid()->toString();

        // إنشاء الجلسة
        $session = new DoorSession();
        $session->device_id = $device->id;
        $session->session_token = $token;
        $session->expires_at = Carbon::now()->addSeconds(60); // تنتهي بعد دقيقة
        $session->is_used = false;
        $session->save();

        return $session;
    }
}
