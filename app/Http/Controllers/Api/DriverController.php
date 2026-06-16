<?php

namespace App\Http\Controllers\Api;

use App\Events\DriverLocationUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

        return response()->json(['success' => true]);
    }
}
