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
        $movementsQuery = StockMovement::where('created_at', '>=', today());

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

        return view('warehouse.dashboard', compact('totalWarehouses', 'totalItems', 'todayInbound', 'todayOutbound'));
    }
}
