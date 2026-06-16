<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Order;
use App\Models\Vehicle;
use App\Models\DriverProfile;
use App\Models\RouteVersion;
use App\Models\Warehouse;
use App\Services\ShipmentService;
use Illuminate\Http\Request;
use Exception;

class ShipmentController extends Controller
{
    protected $shipmentService;

    public function __construct(ShipmentService $shipmentService)
    {
        $this->shipmentService = $shipmentService;
    }

    public function index()
    {
        $shipments = Shipment::with(['driver.user', 'vehicle', 'orders'])->latest()->paginate(10);
        return view('shipments.index', compact('shipments'));
    }

    public function create(Request $request)
    {
        $orders = Order::with('customer', 'originWarehouse', 'currentWarehouse')
            ->whereIn('status', ['Confirmed', 'Arrived at Hub'])
            ->get();
            
        $warehouses = \App\Models\Warehouse::all();
        $vehicles = Vehicle::with('vehicleType')->where('status', 'available')->get();
        $drivers = DriverProfile::where('status', 'available')->get();
        $routeVersions = RouteVersion::with('route')->latest()->get();
        $tariffs = \App\Models\Tariff::all();

        return view('shipments.create', compact('orders', 'warehouses', 'vehicles', 'drivers', 'routeVersions', 'tariffs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:driver_profiles,id',
            'route_mode' => 'required|in:transit,direct',
            'route_version_id' => 'required_if:route_mode,transit|nullable|exists:route_versions,id',
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'exists:orders,id',
            // Fields needed if direct mode
            'direct_distance' => 'nullable|numeric',
            'direct_duration' => 'nullable|numeric',
            'direct_geojson' => 'nullable|json',
        ]);

        try {
            $shipmentData = $request->only(['vehicle_id', 'driver_id', 'total_cost', 'total_distance_km']);
            
            if ($request->route_mode === 'direct') {
                if (count($request->order_ids) > 1) {
                    throw new \Exception("Pengiriman langsung (Direct) hanya boleh memuat 1 Order.");
                }
                
                // Create Ad-hoc Route via RouteService
                $order = Order::with(['originWarehouse', 'currentWarehouse'])->find($request->order_ids[0]);
                
                $routeService = app(\App\Services\RouteService::class);
                
                $startWarehouse = $order->currentWarehouse ?? $order->originWarehouse;
                
                $waypoints = [
                    [(float)$startWarehouse->longitude, (float)$startWarehouse->latitude],
                    [(float)$order->destination_longitude, (float)$order->destination_latitude]
                ];

                $routeData = [
                    'route_code' => 'RTE-ADHOC-' . date('Ymd-His'),
                    'route_type' => 'land',
                    'origin_name' => $startWarehouse->name,
                    'destination_name' => $order->destination_address,
                    'is_master' => false
                ];

                // Since RouteService calculates internally if distance is not provided, 
                // but we already have distance/geojson from frontend, we can bypass or pass it.
                // Assuming RouteService `createRoute` signature takes ($validated, $waypoints) and does the calculation.
                $route = $routeService->createRoute($routeData, $waypoints);
                
                $shipmentData['route_version_id'] = $route->routeVersions()->latest()->first()->id;
            } else {
                $shipmentData['route_version_id'] = $request->route_version_id;
            }
            
            $shipment = $this->shipmentService->createShipment($shipmentData, $request->order_ids);

            return redirect()->route('shipments.index')->with('success', 'Shipment ' . $shipment->shipment_number . ' successfully created.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Shipment $shipment)
    {
        $shipment->load(['driver.user', 'vehicle', 'checkpoints', 'routeVersion.route', 'orders' => function($q) {
            $q->withPivot('status', 'dropoff_warehouse_id');
        }, 'orders.customer', 'orders.originWarehouse', 'orders.currentWarehouse']);
        $warehouses = Warehouse::all();
        return view('shipments.show', compact('shipment', 'warehouses'));
    }

    public function start(Shipment $shipment)
    {
        if ($shipment->status !== 'Pending') {
            return back()->with('error', 'Only Pending shipments can be started.');
        }

        $shipment->load('routeVersion');
        
        $shipment->status = 'On Process';
        $shipment->started_at = now();
        
        // Calculate SLA target
        // Get duration in minutes, add 20% buffer for traffic/rest
        $durationMin = $shipment->routeVersion->duration_min ?? 0;
        $bufferedDuration = $durationMin * 1.2;
        
        $shipment->sla_target_at = now()->addMinutes($bufferedDuration);
        $shipment->save();

        if ($shipment->vehicle) {
            $shipment->vehicle->status = 'on_trip';
            $shipment->vehicle->save();
        }

        // Update underlying orders
        foreach ($shipment->orders as $order) {
            $order->status = 'In Transit';
            $order->save();
        }

        return back()->with('success', 'Shipment has started. SLA target set to ' . $shipment->sla_target_at->format('d M Y, H:i'));
    }

    public function complete(Shipment $shipment)
    {
        $shipment->load(['routeVersion.route', 'orders']);
        $shipment->status = 'Delivered';
        $shipment->completed_at = now();
        $shipment->save();

        if ($shipment->vehicle) {
            $shipment->vehicle->status = 'available';
            $shipment->vehicle->save();
        }

        $isMaster = false;
        if ($shipment->routeVersion && $shipment->routeVersion->route) {
            $isMaster = !str_starts_with($shipment->routeVersion->route->route_code, 'RTE-ADHOC-');
        }
        
        // Find destination warehouse if it's a master route
        $destWarehouseId = null;
        if ($isMaster) {
            $destName = $shipment->routeVersion->route->destination_name;
            // Clean up name (remove " (Warehouse)" if present)
            $cleanName = trim(str_replace('(Warehouse)', '', $destName));
            $destWarehouse = \App\Models\Warehouse::where('name', 'LIKE', '%' . $cleanName . '%')->first();
            if ($destWarehouse) {
                $destWarehouseId = $destWarehouse->id;
            }
        }

        foreach ($shipment->orders as $order) {
            if ($isMaster) {
                // Transit Mode -> Order arrives at Hub
                $order->status = 'Arrived at Hub';
                if ($destWarehouseId) {
                    $order->current_warehouse_id = $destWarehouseId;
                }
            } else {
                // Direct Mode -> Order is delivered to customer
                $order->status = 'Completed';
            }
            $order->save();
            
            // Update pivot
            $shipment->orders()->updateExistingPivot($order->id, ['status' => 'Delivered']);
        }

        return redirect()->route('shipments.index')->with('success', 'Shipment marked as completed. Vehicle is now available and Orders updated.');
    }

    public function unload(Request $request, Shipment $shipment)
    {
        $request->validate([
            'dropoff_warehouse_id' => 'required|exists:warehouses,id',
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'exists:orders,id'
        ]);

        if ($shipment->status !== 'On Process') {
            return back()->with('error', 'Only active shipments (On Process) can be unloaded.');
        }

        try {
            $this->shipmentService->unloadOrders($shipment, $request->order_ids, $request->dropoff_warehouse_id);
            return back()->with('success', count($request->order_ids) . ' orders have been successfully unloaded to the transit hub.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
