<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Search\WarehouseSearchRequest;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;
use App\QueryFilters\WarehouseFilter;

class WarehouseSearchController extends Controller
{
    public function __invoke(WarehouseSearchRequest $request, WarehouseFilter $filter)
    {
        $warehouses = Warehouse::with('users')
            ->filter($filter)
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return WarehouseResource::collection($warehouses);
    }
}
