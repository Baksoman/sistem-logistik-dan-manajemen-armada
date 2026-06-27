<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Search\RouteSearchRequest;
use App\Http\Resources\RouteResource;
use App\Models\Route;
use App\QueryFilters\RouteFilter;

/**
 * API endpoint: GET /api/search/routes
 *
 * Route versions are eagerly loaded ordered by calculated_at DESC
 * so that routeVersions->first() in the Resource always returns the most recent one.
 * The polyline GeoJSON is NOT included in the list response (too large).
 */
class RouteSearchController extends Controller
{
    public function __invoke(RouteSearchRequest $request, RouteFilter $filter)
    {
        $routes = Route::with([
                'routeVersions' => fn($q) => $q
                    ->select(['id', 'route_id', 'source_api', 'distance_km', 'duration_min', 'calculated_at'])
                    ->orderBy('calculated_at', 'desc'),
            ])
            ->filter($filter)
            ->where('is_master', true)
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return RouteResource::collection($routes);
    }
}
