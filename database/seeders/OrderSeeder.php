<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockItem;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $warehouses = Warehouse::all();
        $users = User::all();
        $stockItems = StockItem::all();

        if ($customers->isEmpty() || $warehouses->isEmpty() || $users->isEmpty() || $stockItems->isEmpty()) {
            return;
        }

        // Create a few orders
        for ($i = 1; $i <= 5; $i++) {
            $customer = $customers->random();
            $originWarehouse = $warehouses->random();
            $user = $users->random();

            $order = Order::create([
                'id' => Str::uuid(),
                'order_number' => 'ORD-' . strtoupper(Str::random(6)),
                'customer_id' => $customer->id,
                'created_by' => $user->id,
                'origin_warehouse_id' => $originWarehouse->id,
                'destination_address' => 'Jl. Tujuan No. ' . rand(1, 100) . ', Kota Contoh',
                'destination_latitude' => -7.250445 + (rand(-100, 100) / 10000),
                'destination_longitude' => 112.768845 + (rand(-100, 100) / 10000),
                'total_weight' => 0, // will be calculated below
                'total_volume' => 0, // will be calculated below
                'quoted_price' => rand(500000, 2000000),
                'estimated_distance_km' => rand(10, 500),
                'status' => 'Pending Approval',
                'current_warehouse_id' => $originWarehouse->id,
                'tracking_status' => 'Order Created',
                'created_at' => now()->subDays(rand(2, 5))->subHours(rand(1, 12)),
            ]);

            $totalWeight = 0;
            $totalVolume = 0;

            // Create 1-3 items for each order
            for ($j = 1; $j <= rand(1, 3); $j++) {
                $stockItem = $stockItems->random();
                $qty = rand(1, 10);
                $weight = $qty * 2.5; // dummy weight
                $volume = $qty * 0.5; // dummy volume

                OrderItem::create([
                    'id' => Str::uuid(),
                    'order_id' => $order->id,
                    'stock_item_id' => $stockItem->id,
                    'quantity' => $qty,
                    'weight_kg' => $weight,
                    'volume_cbm' => $volume,
                ]);

                $totalWeight += $weight;
                $totalVolume += $volume;
            }

            $order->update([
                'total_weight' => $totalWeight,
                'total_volume' => $totalVolume,
            ]);
        }
    }
}
