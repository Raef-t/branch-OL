<?php


namespace Modules\DoorSessions\Services;


use Modules\DoorDevices\Models\DoorDevice;
use Modules\DoorSessions\Models\DoorSession;
use Illuminate\Support\Str;
use Carbon\Carbon;


class GenerateDoorSessionService
{
/**
* Create a new DoorSession (QR token) for the given device.
*
* @param DoorDevice $device
* @param int $ttlSeconds
* @return DoorSession
*/
public function handle(DoorDevice $device, int $ttlSeconds = 60): DoorSession
{
// Optionally expire or mark previous sessions
// You could add logic here to invalidate older sessions if needed


// Generate a new unique session token
$token = Str::uuid()->toString();
$expiresAt = Carbon::now()->addSeconds($ttlSeconds);


// Create and return the new session
return DoorSession::create([
'device_id' => $device->device_id,
'session_token' => $token,
'expires_at' => $expiresAt,
'is_used' => false,
]);
}
}