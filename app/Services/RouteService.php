<?php

namespace App\Services;

use App\Models\Route;
use App\Models\RouteVersion;

class RouteService
{
    public function __construct(protected RoutingService $routingService) {}

    public function getPaginatedRoutes($perPage = 10)
    {
        return Route::with('routeVersions')->latest()->paginate($perPage);
    }

    public function getRouteById($id)
    {
        return Route::with(['routeVersions' => function($q) {
            $q->orderBy('calculated_at', 'desc');
        }])->findOrFail($id);
    }

    public function createRoute(array $data, array $waypoints)
    {
        $route = Route::create([
            'route_code' => $data['route_code'],
            'route_type' => $data['route_type'],
            'origin_name' => $data['origin_name'],
            'destination_name' => $data['destination_name'],
            'toll_cost' => $data['toll_cost'] ?? 0,
            'ferry_cost' => $data['ferry_cost'] ?? 0,
        ]);

        $this->createRouteVersion($route, $waypoints);

        return $route;
    }

    public function createRouteVersion(Route $route, array $waypoints)
    {
        if ($route->route_type === 'land') {
            $calculation = $this->routingService->calculateLandRoute($waypoints);
        } elseif ($route->route_type === 'sea') {
            $calculation = $this->routingService->calculateSeaRoute($waypoints[0], end($waypoints));
            $calculation['duration_min'] = ($calculation['distance_km'] / 37) * 60;
        } else {
            // "auto" or "combined"
            $calculation = $this->routingService->calculateCombinedRoute($waypoints);
        }

        return RouteVersion::create([
            'route_id' => $route->id,
            'source_api' => $calculation['source_api'],
            'distance_km' => $calculation['distance_km'],
            'duration_min' => $calculation['duration_min'],
            'polyline_geojson' => $calculation['geojson'],
            'waypoints' => $calculation['waypoints'],
            'calculated_at' => now(),
        ]);
    }

    public function deleteRoute(Route $route)
    {
        $route->routeVersions()->delete();
        return $route->delete();
    }
}
