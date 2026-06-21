<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\StockItem;
use App\Models\StockMovement;

class WarehouseDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $warehousesQuery = Warehouse::where('is_active', true);
        $stockItemsQuery = StockItem::query();
        $movementsQuery = StockMovement::whereDate('created_at', today());

        if ($user && !$user->hasRole('Super Admin')) {
            $warehousesQuery->whereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
            
            $stockItemsQuery->whereHas('warehouse.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
            
            $movementsQuery->whereHas('stockItem.warehouse.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        $totalWarehouses = $warehousesQuery->count();
        $totalItems = $stockItemsQuery->sum('quantity');
        $todayInbound = (clone $movementsQuery)->where('type', 'inbound')->sum('quantity');
        $todayOutbound = (clone $movementsQuery)->where('type', 'outbound')->sum('quantity');

        // Recent Activities
        $recentActivitiesQuery = StockMovement::with(['stockItem', 'creator'])->latest();
        if ($user && !$user->hasRole('Super Admin')) {
            $recentActivitiesQuery->whereHas('stockItem.warehouse.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }
        $recentActivities = $recentActivitiesQuery->take(6)->get();

        // Low Stock Items
        $lowStockQuery = clone $stockItemsQuery;
        $lowStockItems = $lowStockQuery->whereColumn('quantity', '<=', 'min_quantity')->with('warehouse')->take(6)->get();

        // Trend Data (Last 7 Days)
        $trendData = [
            'labels' => [],
            'inbound' => [],
            'outbound' => []
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $trendData['labels'][] = $date->format('d M');
            
            $dayMovementsQuery = StockMovement::whereDate('created_at', $date);
            if ($user && !$user->hasRole('Super Admin')) {
                $dayMovementsQuery->whereHas('stockItem.warehouse.users', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
            }

            $trendData['inbound'][] = (clone $dayMovementsQuery)->where('type', 'inbound')->sum('quantity');
            $trendData['outbound'][] = (clone $dayMovementsQuery)->where('type', 'outbound')->sum('quantity');
        }

        // Advanced Analytics
        // 1. Inventory Turnover (30 days outbound / Total Stock)
        $last30DaysOutboundQuery = StockMovement::where('type', 'outbound')->where('created_at', '>=', now()->subDays(30));
        if ($user && !$user->hasRole('Super Admin')) {
            $last30DaysOutboundQuery->whereHas('stockItem.warehouse.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }
        $last30DaysOutbound = $last30DaysOutboundQuery->sum('quantity');
        
        $inventoryTurnover = $totalItems > 0 ? ($last30DaysOutbound / $totalItems) : 0;

        // 2. Space / Capacity Utilization
        $totalRacksQuery = \App\Models\Rack::query();
        if ($user && !$user->hasRole('Super Admin')) {
            $totalRacksQuery->whereHas('zone.warehouse.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }
        $totalRacks = $totalRacksQuery->count();
        
        $occupiedRacksQuery = StockItem::whereNotNull('rack_id')->distinct('rack_id');
        if ($user && !$user->hasRole('Super Admin')) {
            $occupiedRacksQuery->whereHas('warehouse.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }
        $occupiedRacks = $occupiedRacksQuery->count('rack_id');
        
        $spaceUtilization = $totalRacks > 0 ? ($occupiedRacks / $totalRacks) * 100 : 0;

        // 3. Order Cycle Time (Avg hours between Order creation and Shipment start)
        $completedShipmentsQuery = \App\Models\Shipment::whereNotNull('started_at')->whereNotNull('completed_at')->whereHas('orders');
        if ($user && !$user->hasRole('Super Admin')) {
            $warehouseIds = $user->warehouses()->pluck('warehouses.id')->toArray();
            $completedShipmentsQuery->whereHas('orders', function($q) use ($warehouseIds){
                $q->whereIn('origin_warehouse_id', $warehouseIds);
            });
        }
        $completedShipments = $completedShipmentsQuery->with('orders')->take(100)->get();
        
        $totalCycleTimeHours = 0;
        $cycleCount = 0;
        foreach ($completedShipments as $shipment) {
            foreach ($shipment->orders as $order) {
                $created = \Carbon\Carbon::parse($order->created_at);
                $started = \Carbon\Carbon::parse($shipment->started_at);
                if ($started->greaterThan($created)) {
                    $totalCycleTimeHours += $created->diffInHours($started);
                    $cycleCount++;
                }
            }
        }
        $orderCycleTime = $cycleCount > 0 ? ($totalCycleTimeHours / $cycleCount) : 0;

        $warehouseAnalytics = [
            'inventory_turnover' => $inventoryTurnover,
            'space_utilization' => $spaceUtilization,
            'order_cycle_time' => $orderCycleTime,
        ];

        return view('warehouse.dashboard', compact(
            'totalWarehouses', 
            'totalItems', 
            'todayInbound', 
            'todayOutbound',
            'recentActivities',
            'lowStockItems',
            'trendData',
            'warehouseAnalytics'
        ));
    }
}
