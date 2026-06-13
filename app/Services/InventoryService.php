<?php

namespace App\Services;

use App\Models\StockItem;
use App\Models\ItemCategory;
use App\Models\UnitType;
use App\Models\Warehouse;
use App\Models\Zone;
use App\Models\Rack;

class InventoryService
{
    public function getPaginatedStockItems($perPage = 10)
    {
        $user = auth()->user();
        $query = StockItem::with(['warehouse', 'category', 'unitType'])->latest();

        if ($user && !$user->hasRole('Super Admin')) {
            $query->whereHas('warehouse.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        return $query->paginate($perPage);
    }

    public function getWarehouses()
    {
        $user = auth()->user();
        $query = Warehouse::where('is_active', true);

        if ($user && !$user->hasRole('Super Admin')) {
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        return $query->get();
    }

    public function getItemCategories()
    {
        return ItemCategory::all();
    }

    public function getUnitTypes()
    {
        return UnitType::all();
    }

    public function getZones()
    {
        return Zone::all();
    }

    public function getRacks()
    {
        return Rack::all();
    }

    public function createStockItem(array $data)
    {
        return StockItem::create($data);
    }

    public function updateStockItem(StockItem $stockItem, array $data)
    {
        return $stockItem->update($data);
    }

    public function deleteStockItem(StockItem $stockItem)
    {
        return $stockItem->delete();
    }
}
