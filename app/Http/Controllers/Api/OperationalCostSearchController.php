<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Search\OperationalCostSearchRequest;
use App\Http\Resources\OperationalCostResource;
use App\Models\OperationalCost;
use App\QueryFilters\OperationalCostFilter;

class OperationalCostSearchController extends Controller
{
    public function __invoke(OperationalCostSearchRequest $request, OperationalCostFilter $filter)
    {
        $costs = OperationalCost::with(['shipment', 'category', 'shipment.driver.user'])
            ->filter($filter)
            ->latest('recorded_at')
            ->paginate($request->integer('per_page', 15));

        return OperationalCostResource::collection($costs);
    }
}
