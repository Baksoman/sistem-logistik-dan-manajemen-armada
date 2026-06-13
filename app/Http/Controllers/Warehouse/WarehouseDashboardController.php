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

        return view('warehouse.dashboard', compact(
            'totalWarehouses', 
            'totalItems', 
            'todayInbound', 
            'todayOutbound',
            'recentActivities',
            'lowStockItems',
            'trendData'
        ));
    }
}
