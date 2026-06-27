<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Search\DriverSearchRequest;
use App\Http\Resources\DriverResource;
use App\Models\DriverProfile;
use App\QueryFilters\DriverFilter;

/**
 * API endpoint: GET /api/search/drivers
 *
 * Note on eager loading:
 * DriverProfile → User is a belongsTo relation.
 * We load 'user' to populate name/email fields in the Resource.
 * No further nesting is needed for this endpoint.
 */
class DriverSearchController extends Controller
{
    public function __invoke(DriverSearchRequest $request, DriverFilter $filter)
    {
        $drivers = DriverProfile::with('user')
            ->filter($filter)
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return DriverResource::collection($drivers);
    }
}
