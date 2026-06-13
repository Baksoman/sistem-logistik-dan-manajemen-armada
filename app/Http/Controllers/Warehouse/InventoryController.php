<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\StockItem;
use App\Services\InventoryService;
use App\Http\Requests\Warehouse\StoreStockItemRequest;
use App\Http\Requests\Warehouse\UpdateStockItemRequest;

class InventoryController extends Controller
{
    public function __construct(protected InventoryService $inventoryService) {}

    public function index()
    {
        $inventory = $this->inventoryService->getPaginatedStockItems(10);
        $warehouses = $this->inventoryService->getWarehouses();
        $categories = $this->inventoryService->getItemCategories();
        $unitTypes = $this->inventoryService->getUnitTypes();
        
        return view('warehouse.inventory.index', compact('inventory', 'warehouses', 'categories', 'unitTypes'));
    }

    public function store(StoreStockItemRequest $request)
    {
        $this->inventoryService->createStockItem($request->validated());
        return back()->with('success', 'Stock item added successfully.');
    }

    public function update(UpdateStockItemRequest $request, StockItem $inventory)
    {
        $this->inventoryService->updateStockItem($inventory, $request->validated());
        return back()->with('success', 'Stock item updated successfully.');
    }

    public function destroy(StockItem $inventory)
    {
        try {
            $this->inventoryService->deleteStockItem($inventory);
            return back()->with('success', 'Stock item deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
