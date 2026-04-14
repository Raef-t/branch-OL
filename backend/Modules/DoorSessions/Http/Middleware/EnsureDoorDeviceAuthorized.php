<?php

namespace Modules\DoorSessions\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\DoorDevices\Models\DoorDevice;
use Symfony\Component\HttpFoundation\Response;

class EnsureDoorDeviceAuthorized
{
    /**
     * Handle an incoming request.
     * Checks X-DEVICE-KEY header and device_id parameter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $deviceId = $request->input('device_id') ?? $request->header('X-DEVICE-ID');
        $apiKey   = $request->header('X-DEVICE-KEY');

        if (! $deviceId || ! $apiKey) {
            return response()->json([
                'status'  => false,
                'message' => 'مفتاح الجهاز أو المعرف مفقود.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $device = DoorDevice::where('device_id', $deviceId)
            ->where('is_active', true)
            ->first();

        if (! $device) {
            return response()->json([
                'status'  => false,
                'message' => 'الجهاز غير مسموح أو غير مفعل.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($device->api_key !== $apiKey) {
            return response()->json([
                'status'  => false,
                'message' => 'المفتاح المرسل غير صحيح.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Share the device instance for controller
        $request->attributes->set('door_device', $device);

        return $next($request);
    }
}
