<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Search\OrderSearchRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\QueryFilters\OrderFilter;

/**
 * API endpoint: GET /api/search/orders
 *
 * Data scoping rule:
 * - Super Admin / Admin Logistik: see ALL orders
 * - Staff Warehouse: see ONLY orders originating from their assigned warehouses
 *
 * The scope restriction is applied as a base query constraint BEFORE
 * the user-supplied filters are applied, ensuring the filter can never
 * expose data outside the allowed scope.
 */
class OrderSearchController extends Controller
{
    public function __invoke(OrderSearchRequest $request, OrderFilter $filter)
    {
        $user = auth()->user();

        // Build the base query with appropriate data scope.
        if ($user->hasRole('Staff Warehouse')) {
            // Staff Warehouse can only see orders from their assigned warehouses.
            $warehouseIds = $user->warehouses()->pluck('warehouses.id');
            $query = Order::whereIn('origin_warehouse_id', $warehouseIds);
        } else {
            $query = Order::query();
        }

        $orders = $query
            ->with(['customer', 'originWarehouse', 'creator'])
            ->filter($filter)
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return OrderResource::collection($orders);
    }
}
