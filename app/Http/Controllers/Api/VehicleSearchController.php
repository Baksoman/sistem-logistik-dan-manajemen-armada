<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Search\VehicleSearchRequest;
use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use App\QueryFilters\VehicleFilter;

/**
 * API endpoint: GET /api/search/vehicles
 */
class VehicleSearchController extends Controller
{
    public function __invoke(VehicleSearchRequest $request, VehicleFilter $filter)
    {
        $vehicles = Vehicle::with('vehicleType')
            ->filter($filter)
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return VehicleResource::collection($vehicles);
    }
}
