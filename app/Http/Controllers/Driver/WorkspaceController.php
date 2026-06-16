<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

    public function history()
    {
        $user = auth()->user();
        
        if (!$user->hasRole('driver') || !$user->driverProfile()->exists()) {
            abort(403, 'Unauthorized access.');
        }

        $completedShipments = Shipment::where('driver_id', $user->driverProfile->id)
            ->whereIn('status', ['Delivered', 'Completed', 'Failed'])
            ->with(['vehicle', 'routeVersion', 'orders'])
            ->orderBy('completed_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('driver.workspace.history', compact('completedShipments'));
    }

    public function historyShow(Shipment $shipment)
    {
        $user = auth()->user();
        if (!$user->hasRole('driver') || $shipment->driver_id !== $user->driverProfile->id) {
            abort(403, 'Unauthorized access.');
        }

        $shipment->load([
            'vehicle', 
            'routeVersion', 
            'orders.proofOfDeliveries.podPhotos'
        ]);

        $checkpoints = DB::table('shipment_checkpoints')
            ->where('shipment_id', $shipment->id)
            ->orderBy('recorded_at', 'asc')
            ->get();

        return view('driver.workspace.history-show', compact('shipment', 'checkpoints'));
    }

    public function startJourney(Request $request, Shipment $shipment)
    {
        $user = auth()->user();
        if (!$user->hasRole('driver') || $shipment->driver_id !== $user->driverProfile->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($shipment->status === 'Pending') {
            $shipment->load(['routeVersion', 'orders']);
            
            $durationMin = $shipment->routeVersion->duration_min ?? 0;
            $bufferedDuration = $durationMin * 1.2;

            $shipment->update([
                'status' => 'On Process',
                'started_at' => now(),
                'sla_target_at' => now()->addMinutes($bufferedDuration)
            ]);

            if ($shipment->vehicle) {
                $shipment->vehicle->update(['status' => 'on_trip']);
            }

            foreach ($shipment->orders as $order) {
                if ($order->status === 'Assigned') {
                    $order->update(['status' => 'On Process']);
                    
                    \App\Models\OrderHistory::create([
                        'order_id' => $order->id,
                        'user_id' => $user->id,
                        'status' => 'On Process',
                        'description' => 'Driver has started the journey.',
                    ]);
                }
            }
            
            // Log history to existing shipment_checkpoints table
            DB::table('shipment_checkpoints')->insert([
                'id' => (string) Str::uuid(),
                'shipment_id' => $shipment->id,
                'checkpoint_type' => 'Journey Started',
                'description' => 'Driver started the journey from Origin.',
                'recorded_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Journey started! Please drive safely.');
    }

    public function show(Shipment $shipment)
    {
        $user = auth()->user();
        if (!$user->hasRole('driver') || $shipment->driver_id !== $user->driverProfile->id) {
            abort(403, 'Unauthorized access.');
        }

        $shipment->load(['orders' => function($q) {
            $q->withPivot('status', 'dropoff_warehouse_id');
        }, 'orders.customer', 'routeVersion']);

        return view('driver.workspace.show', compact('shipment'));
    }

    public function packages(Shipment $shipment)
    {
        $user = auth()->user();
        if (!$user->hasRole('driver') || $shipment->driver_id !== $user->driverProfile->id) {
            abort(403, 'Unauthorized access.');
        }

        $shipment->load(['orders' => function($q) {
            $q->withPivot('status', 'dropoff_warehouse_id');
        }, 'orders.customer', 'orders.items.stockItem']);

        $warehouses = \App\Models\Warehouse::all()->keyBy('id');

        return view('driver.workspace.packages', compact('shipment', 'warehouses'));
    }

    public function costs(Shipment $shipment)
    {
        $user = auth()->user();
        if (!$user->hasRole('driver') || $shipment->driver_id !== $user->driverProfile->id) {
            abort(403, 'Unauthorized action.');
        }

        $categories = \App\Models\CostCategory::all();
        $costs = \App\Models\OperationalCost::where('shipment_id', $shipment->id)
            ->with('category')
            ->orderBy('recorded_at', 'desc')
            ->get();

        return view('driver.workspace.costs', compact('shipment', 'categories', 'costs'));
    }

    public function globalCosts()
    {
        $user = auth()->user();
        if (!$user->hasRole('driver') || !$user->driverProfile()->exists()) {
            abort(403, 'Unauthorized access.');
        }

        // Get all shipments assigned to this driver
        $shipmentIds = \App\Models\Shipment::where('driver_id', $user->driverProfile->id)->pluck('id');

        $costs = \App\Models\OperationalCost::whereIn('shipment_id', $shipmentIds)
            ->with(['category', 'shipment.routeVersion.route'])
            ->orderBy('recorded_at', 'desc')
            ->get();

        return view('driver.workspace.costs-global', compact('costs'));
    }

    public function storeCost(Request $request, Shipment $shipment)
    {
        $user = auth()->user();
        if (!$user->hasRole('driver') || $shipment->driver_id !== $user->driverProfile->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'category_id' => 'required|exists:cost_categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'receipt' => 'nullable|image|max:10240',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        \App\Models\OperationalCost::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'shipment_id' => $shipment->id,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'receipt_path' => $receiptPath,
            'recorded_at' => now(),
        ]);

        return back()->with('success', 'Operational cost added successfully!');
    }

    public function unloadPackage(Request $request, Shipment $shipment, \App\Models\Order $order)
    {
        $user = auth()->user();
        if (!$user->hasRole('driver') || $shipment->driver_id !== $user->driverProfile->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'pod_photo' => 'required|image|max:10240',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $photoPath = $request->file('pod_photo')->store('pods', 'public');

        return DB::transaction(function () use ($shipment, $order, $request, $photoPath, $user) {
            $pivot = DB::table('shipment_orders')
                ->where('shipment_id', $shipment->id)
                ->where('order_id', $order->id)
                ->first();

            if (!$pivot) {
                return back()->with('error', 'Package not found in this shipment.');
            }

            $isTransit = false;
            $destWarehouseId = null;
            if ($shipment->routeVersion && $shipment->routeVersion->route) {
                $isTransit = !str_starts_with($shipment->routeVersion->route->route_code, 'RTE-ADHOC-');
                if ($isTransit) {
                    $destName = $shipment->routeVersion->route->destination_name;
                    $cleanName = trim(str_replace('(Warehouse)', '', $destName));
                    $destWarehouse = \App\Models\Warehouse::where('name', 'LIKE', '%' . $cleanName . '%')->first();
                    if ($destWarehouse) {
                        $destWarehouseId = $destWarehouse->id;
                    }
                }
            }

            $newPivotStatus = $isTransit ? 'Unloaded' : 'Delivered';
            $newOrderStatus = $isTransit ? 'Arrived at Hub' : 'Completed';

            DB::table('shipment_orders')
                ->where('shipment_id', $shipment->id)
                ->where('order_id', $order->id)
                ->update([
                    'status' => $newPivotStatus,
                    'dropoff_warehouse_id' => $destWarehouseId
                ]);

            $order->status = $newOrderStatus;
            if ($isTransit && $destWarehouseId) {
                $order->current_warehouse_id = $destWarehouseId;
            }
            $order->save();

            $pod = \App\Models\ProofOfDelivery::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'shipment_id' => $shipment->id,
                'order_id' => $order->id,
                'receiver_name' => $request->receiver_name,
                'receiver_phone' => $request->receiver_phone,
                'notes' => $request->notes,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'delivered_at' => now(),
            ]);

            \App\Models\PodPhoto::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'proof_of_delivery_id' => $pod->id,
                'photo_path' => $photoPath,
                'uploaded_at' => now(),
            ]);

            \App\Models\OrderHistory::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'order_id' => $order->id,
                'status' => $newOrderStatus,
                'description' => "Package unloaded by driver " . $user->name . " and received by " . $request->receiver_name,
                'location' => $isTransit ? 'Hub Transit' : 'Destination',
                'user_id' => $user->id,
            ]);

            DB::table('shipment_checkpoints')->insert([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'shipment_id' => $shipment->id,
                'checkpoint_type' => 'Package Unloaded',
                'description' => "Unloaded package {$order->order_number} to {$request->receiver_name}.",
                'recorded_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $pendingOrders = DB::table('shipment_orders')
                ->where('shipment_id', $shipment->id)
                ->whereIn('status', ['Pending', 'On Process', 'Loaded'])
                ->count();

            if ($pendingOrders === 0) {
                $shipment->update([
                    'status' => 'Delivered',
                    'completed_at' => now()
                ]);
                
                if ($shipment->vehicle) {
                    $shipment->vehicle->update(['status' => 'available']);
                }
                
                DB::table('shipment_checkpoints')->insert([
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'shipment_id' => $shipment->id,
                    'checkpoint_type' => 'Journey Completed',
                    'description' => 'Driver completed the journey and unloaded all packages.',
                    'recorded_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                return redirect()->route('driver.workspace.index')->with('success', 'All packages unloaded! Journey Completed.');
            }

            return back()->with('success', "Package {$order->order_number} successfully unloaded and Proof of Delivery saved!");
        });
    }
}
