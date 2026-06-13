<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use App\Http\Requests\Warehouse\StoreWarehouseRequest;
use App\Http\Requests\Warehouse\UpdateWarehouseRequest;

class WarehouseController extends Controller
{
    public function __construct(protected WarehouseService $warehouseService) {}

    public function index()
    {
        $warehouses = $this->warehouseService->getPaginatedWarehouses(10);
        return view('warehouse.warehouses.index', compact('warehouses'));
    }

    public function store(StoreWarehouseRequest $request)
    {
        $this->warehouseService->createWarehouse($request->validated());
        return back()->with('success', 'Warehouse created successfully.');
    }

    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse)
    {
        $this->warehouseService->updateWarehouse($warehouse, $request->validated());
        return back()->with('success', 'Warehouse updated successfully.');
    }

    public function destroy(Warehouse $warehouse)
    {
        try {
            $this->warehouseService->deleteWarehouse($warehouse);
            return back()->with('success', 'Warehouse deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
