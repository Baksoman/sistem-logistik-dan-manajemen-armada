<?php

namespace App\Services;

use App\Models\StockItem;
use App\Models\ItemCategory;
use App\Models\UnitType;
use App\Models\Warehouse;

class InventoryService
{
    public function getPaginatedStockItems($perPage = 10)
    {
        return StockItem::with(['warehouse', 'category', 'unitType'])->latest()->paginate($perPage);
    }

    public function getWarehouses()
    {
        return Warehouse::where('is_active', true)->get();
    }

    public function getItemCategories()
    {
        return ItemCategory::all();
    }

    public function getUnitTypes()
    {
        return UnitType::all();
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
