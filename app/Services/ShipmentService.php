<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\Order;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Exception;

class ShipmentService
{
    /**
     * Create a new shipment and assign orders to it.
     *
     * @param array $shipmentData
     * @param array $orderIds
     * @return Shipment
     * @throws Exception
     */
    public function createShipment(array $shipmentData, array $orderIds): Shipment
    {
        return DB::transaction(function () use ($shipmentData, $orderIds) {
            // Lock vehicle to prevent double assignment
            $vehicle = Vehicle::lockForUpdate()->find($shipmentData['vehicle_id']);
            if (!$vehicle) {
                throw new Exception("Vehicle not found.");
            }

            if ($vehicle->status !== 'available') {
                throw new Exception("Vehicle is not available. Current status: {$vehicle->status}");
            }

            // Lock orders
            $orders = Order::whereIn('id', $orderIds)->lockForUpdate()->get();
            
            if ($orders->count() !== count($orderIds)) {
                throw new Exception("One or more orders are missing or invalid.");
            }

            // Verify order status
            foreach ($orders as $order) {
                if (!in_array($order->status, ['Confirmed', 'Pending Approval', 'Arrived at Hub'])) {
                    // Just simple check, in reality it should only be Confirmed or Arrived at Hub
                    throw new Exception("Order {$order->order_number} is not ready for shipment (Status: {$order->status}).");
                }
            }

            $totalWeight = $orders->sum('total_weight');
            $totalVolume = $orders->sum('total_volume');

            if ($totalWeight > $vehicle->capacity_kg) {
                throw new Exception("Overload: Total weight ({$totalWeight}kg) exceeds vehicle capacity ({$vehicle->capacity_kg}kg).");
            }

            if ($totalVolume > $vehicle->capacity_volume_cbm) {
                throw new Exception("Overload: Total volume ({$totalVolume}cbm) exceeds vehicle capacity ({$vehicle->capacity_volume_cbm}cbm).");
            }

            // Generate shipment number
            $shipmentData['shipment_number'] = 'SHP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
            $shipmentData['status'] = 'Pending';
            
            $shipment = Shipment::create($shipmentData);

            // Assign Orders to Shipment
            foreach ($orders as $order) {
                $shipment->orders()->attach($order->id, [
                    'status' => 'Loaded'
                ]);

                // Update order status
                $order->status = 'Assigned';
                $order->save();
            }

            // Update vehicle status
            $vehicle->status = 'on_trip';
            $vehicle->save();

            return $shipment;
        });
    }

    public function unloadOrders(Shipment $shipment, array $orderIds, string $dropoffWarehouseId): Shipment
    {
        return DB::transaction(function () use ($shipment, $orderIds, $dropoffWarehouseId) {
            // Lock orders being unloaded
            $orders = Order::whereIn('id', $orderIds)->lockForUpdate()->get();

            foreach ($orders as $order) {
                // Update pivot table (shipment_orders)
                $shipment->orders()->updateExistingPivot($order->id, [
                    'status' => 'Unloaded',
                    'dropoff_warehouse_id' => $dropoffWarehouseId
                ]);

                // Update order master table
                $order->current_warehouse_id = $dropoffWarehouseId;
                $order->status = 'Arrived at Hub';
                $order->save();
            }

            // Check if there are any orders still 'Loaded'
            $remainingLoaded = $shipment->orders()->wherePivot('status', 'Loaded')->count();

            if ($remainingLoaded === 0) {
                // If no more loaded orders, mark shipment as Delivered (job done)
                $shipment->status = 'Delivered';
                $shipment->completed_at = now();
                $shipment->save();
                
                // Free the vehicle
                if ($shipment->vehicle) {
                    $shipment->vehicle->status = 'available';
                    $shipment->vehicle->save();
                }
            }

            return $shipment;
        });
    }
}
