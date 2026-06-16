<?php

namespace App\Http\Controllers\Api;

use App\Events\DriverLocationUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class DriverController extends Controller
{
    public function pingLocation(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'shipment_id' => 'required|exists:shipments,id'
        ]);

        $user = auth()->user();
        if (!$user->hasRole('driver') || !$user->driverProfile()->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        event(new DriverLocationUpdated($request->shipment_id, $request->lat, $request->lng));

        $gpsData = [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'shipment_id' => $request->shipment_id,
            'driver_id' => $user->driverProfile->id,
            'latitude' => $request->lat,
            'longitude' => $request->lng,
            'speed' => null,
            'heading' => null,
            'recorded_at' => now()->toDateTimeString(),
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];
        
        Redis::rpush('gps_buffer', json_encode($gpsData));

        broadcast(new \App\Events\DriverLocationUpdated($request->shipment_id, $request->lat, $request->lng));

        return response()->json(['success' => true]);
    }
}
