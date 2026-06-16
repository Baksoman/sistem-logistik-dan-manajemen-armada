<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Ensure user is a driver
        if (!$user->hasRole('driver') || !$user->driverProfile()->exists()) {
            abort(403, 'Unauthorized access. You must be a registered driver.');
        }
        
        $activeShipments = Shipment::where('driver_id', $user->driverProfile->id)
            ->whereIn('status', ['Pending', 'On Process'])
            ->with(['vehicle', 'routeVersion', 'orders.customer'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('driver.workspace.index', compact('activeShipments'));
    }

    public function startJourney(Request $request, Shipment $shipment)
    {
        $user = auth()->user();
        if (!$user->hasRole('driver') || $shipment->driver_id !== $user->driverProfile->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($shipment->status === 'Pending') {
            $shipment->update(['status' => 'On Process']);
            
            // Log history
            $shipment->statusHistories()->create([
                'status' => 'On Process',
                'description' => 'Driver started the journey.',
                'user_id' => $user->id
            ]);
        }

        return back()->with('success', 'Journey started! Please drive safely.');
    }
}
