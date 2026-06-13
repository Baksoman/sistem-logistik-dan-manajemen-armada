<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\Role;
use App\Services\WarehouseService;
use App\Http\Requests\Warehouse\StoreWarehouseRequest;
use App\Http\Requests\Warehouse\UpdateWarehouseRequest;

class WarehouseController extends Controller
{
    public function __construct(protected WarehouseService $warehouseService) {}

    public function index()
    {
        $warehouses = $this->warehouseService->getPaginatedWarehouses(10);

        // Get users who can be assigned to warehouses (Staff Warehouse role)
        $warehouseRole = Role::where('name', 'Staff Warehouse')->first();
        $assignableUsers = $warehouseRole
            ? User::whereHas('roles', fn($q) => $q->where('roles.id', $warehouseRole->id))->get()
            : collect();

        return view('warehouse.warehouses.index', compact('warehouses', 'assignableUsers'));
    }

    public function store(StoreWarehouseRequest $request)
    {
        $warehouse = $this->warehouseService->createWarehouse($request->validated());

        // Sync user mapping
        if ($request->has('user_ids')) {
            $warehouse->users()->sync($request->input('user_ids', []));
        }

        return back()->with('success', 'Warehouse created successfully.');
    }

    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse)
    {
        $this->warehouseService->updateWarehouse($warehouse, $request->validated());

        // Sync user mapping
        $warehouse->users()->sync($request->input('user_ids', []));

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
