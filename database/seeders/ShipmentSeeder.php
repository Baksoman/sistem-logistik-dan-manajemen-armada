<?php

namespace Database\Seeders;

use App\Models\DriverProfile;
use App\Models\Order;
use App\Models\RouteVersion;
use App\Models\Shipment;
use App\Models\Vehicle;
use App\Models\ShipmentCheckpoint;
use App\Models\GpsHistory;
use App\Models\ProofOfDelivery;
use App\Models\PodPhoto;
use App\Models\OperationalCost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ShipmentSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = DriverProfile::all();
        $vehicles = Vehicle::all();
        $routeVersions = RouteVersion::all();
        $orders = Order::all();

        if ($drivers->isEmpty() || $vehicles->isEmpty() || $routeVersions->isEmpty() || $orders->isEmpty()) {
            return;
        }

        // Create a few shipments
        for ($i = 1; $i <= 3; $i++) {
            $driver = $drivers->random();
            $vehicle = $vehicles->random();
            $routeVersion = $routeVersions->random();

            $shipment = Shipment::create([
                'id' => Str::uuid(),
                'shipment_number' => 'SHP-' . strtoupper(Str::random(6)),
                'driver_id' => $driver->id,
                'vehicle_id' => $vehicle->id,
                'route_version_id' => $routeVersion->id,
                'status' => 'Delivered', // For the sake of complete data
                'total_distance_km' => $routeVersion->distance_km,
                'total_cost' => rand(1000000, 5000000),
                'cost_per_km' => rand(5000, 15000),
                'sla_target_at' => now()->addDays(2),
                'started_at' => now()->subDays(1),
                'completed_at' => now(),
            ]);

            // Assign 1-2 orders to this shipment
            $assignedOrders = $orders->random(rand(1, 2));
            foreach ($assignedOrders as $order) {
                DB::table('shipment_orders')->insert([
                    'shipment_id' => $shipment->id,
                    'order_id' => $order->id,
                    'status' => 'Delivered',
                    'dropoff_warehouse_id' => null,
                ]);

                // Update order status
                $order->update(['status' => 'Completed']);
            }

            // Create Checkpoints
            ShipmentCheckpoint::create([
                'id' => Str::uuid(),
                'shipment_id' => $shipment->id,
                'checkpoint_type' => 'Loaded',
                'description' => 'Barang dimuat ke kendaraan di Origin Warehouse',
                'latitude' => -7.250445,
                'longitude' => 112.768845,
                'recorded_at' => now()->subDays(1),
            ]);

            ShipmentCheckpoint::create([
                'id' => Str::uuid(),
                'shipment_id' => $shipment->id,
                'checkpoint_type' => 'Delivered',
                'description' => 'Barang tiba di tujuan',
                'latitude' => -7.983908,
                'longitude' => 112.621391,
                'recorded_at' => now(),
            ]);

            // Create GPS History
            GpsHistory::create([
                'id' => Str::uuid(),
                'shipment_id' => $shipment->id,
                'driver_id' => $driver->id,
                'latitude' => -7.500000,
                'longitude' => 112.700000,
                'speed' => 60,
                'recorded_at' => now()->subHours(12),
            ]);

            // Create Proof of Delivery
            $pod = ProofOfDelivery::create([
                'id' => Str::uuid(),
                'shipment_id' => $shipment->id,
                'receiver_name' => 'Penerima Contoh ' . $i,
                'delivered_at' => now(),
                'notes' => 'Diterima dalam kondisi baik',
            ]);

            // Create POD Photo
            PodPhoto::create([
                'id' => Str::uuid(),
                'proof_of_delivery_id' => $pod->id,
                'photo_path' => 'pods/photos/dummy_photo_' . $i . '.png',
                'uploaded_at' => now(),
            ]);

            // Create Operational Cost (Random)
            OperationalCost::create([
                'id' => Str::uuid(),
                'shipment_id' => $shipment->id,
                'category_id' => DB::table('cost_categories')->inRandomOrder()->value('id'),
                'amount' => rand(50000, 200000),
                'description' => 'Biaya Operasional Acak',
                'receipt_path' => null,
                'recorded_at' => now(),
            ]);

            // Create Operational Cost (Specific for Fuel)
            $fuelCategoryId = DB::table('cost_categories')->where('name', 'Fuel')->value('id');
            if ($fuelCategoryId) {
                OperationalCost::create([
                    'id' => Str::uuid(),
                    'shipment_id' => $shipment->id,
                    'category_id' => $fuelCategoryId,
                    'amount' => rand(100000, 300000),
                    'description' => 'Biaya Bahan Bakar',
                    'receipt_path' => null,
                    'recorded_at' => now(),
                ]);
            }
        }
    }
}
