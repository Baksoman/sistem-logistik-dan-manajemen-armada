<?php

namespace App\QueryFilters;

/**
 * Filter class for the Route module.
 *
 * Searchable fields: route_code, origin_name, destination_name
 * Filterable fields: route_type, is_master, source_api (via routeVersions)
 */
class RouteFilter extends QueryFilter
{
    /**
     * Keyword search across the three fields that uniquely identify a route
     * from an operational perspective.
     *
     * - route_code: direct identifier
     * - origin_name / destination_name: how operators typically look up routes
     */
    protected function search(string $value): void
    {
        $this->builder->where(function ($q) use ($value) {
            $q->where('route_code', 'LIKE', "%{$value}%")
              ->orWhere('origin_name', 'LIKE', "%{$value}%")
              ->orWhere('destination_name', 'LIKE', "%{$value}%");
        });
    }

    /**
     * Filter by route type.
     * Valid values: land, sea, combined.
     */
    protected function route_type(string $value): void
    {
        $this->builder->where('route_type', $value);
    }



    /**
     * Filter routes that have at least one version calculated by a specific API.
     * Valid values: OpenRouteService, OSRM, Searoute.
     *
     * Uses whereHas to avoid joining route_versions unnecessarily.
     */
    protected function source_api(string $value): void
    {
        $this->builder->whereHas('routeVersions', function ($q) use ($value) {
            $q->where('source_api', $value);
        });
    }
}
