<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockItem;
use App\Enums\OrderTrackingStatus;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    public function createOrder(array $orderData, array $items): Order
    {
        return DB::transaction(function () use ($orderData, $items) {
            $totalWeight = 0;
            $totalVolume = 0;

            // Prepare order items, calculate totals, and allocate stock
            $preparedItems = [];
            foreach ($items as $itemData) {
                // lockForUpdate() prevents race conditions if multiple users order the same item simultaneously
                $stockItem = StockItem::lockForUpdate()->find($itemData['stock_item_id']);

                if (!$stockItem) {
                    throw new Exception("Stock item not found.");
                }

                $availableQty = $stockItem->quantity - $stockItem->allocated_quantity;

                if ($availableQty < $itemData['quantity']) {
                    throw new Exception("Insufficient stock for item: {$stockItem->name}. Available: {$availableQty}");
                }

                $itemWeight = $stockItem->weight_kg * $itemData['quantity'];
                $itemVolume = $stockItem->volume_cbm * $itemData['quantity'];

                $totalWeight += $itemWeight;
                $totalVolume += $itemVolume;

                // Update stock quantities (Allocate only. Physical quantity remains the same until picked)
                $stockItem->allocated_quantity += $itemData['quantity'];
                $stockItem->save();

                $preparedItems[] = [
                    'stock_item_id' => $stockItem->id,
                    'quantity' => $itemData['quantity'],
                    'weight_kg' => $itemWeight,
                    'volume_cbm' => $itemVolume,
                ];
            }

            // Create Order
            $orderData['total_weight'] = $totalWeight;
            $orderData['total_volume'] = $totalVolume;
            $orderData['tracking_status'] = OrderTrackingStatus::ORDER_CREATED;
            $orderData['current_warehouse_id'] = $orderData['origin_warehouse_id'];
            $orderData['status'] = 'Pending Approval'; // Default to Pending Approval per user request

            // Generate a unique order number
            if (empty($orderData['order_number'])) {
                $orderData['order_number'] = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
            }

            $order = Order::create($orderData);

            // Create Order Items
            foreach ($preparedItems as $preparedItem) {
                $preparedItem['order_id'] = $order->id;
                OrderItem::create($preparedItem);
            }

            return $order;
        });
    }
}
