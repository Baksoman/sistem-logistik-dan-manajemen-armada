<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'route_code'       => $this->route_code,
            'route_type'       => $this->route_type,
            'origin_name'      => $this->origin_name,
            'destination_name' => $this->destination_name,
            'is_master'        => $this->is_master,
            'toll_cost'        => $this->toll_cost,
            'ferry_cost'       => $this->ferry_cost,
            // Include only the most recent route version — avoids bloating the list response
            // with full polyline GeoJSON (which can be megabytes per route).
            'latest_version'   => $this->whenLoaded('routeVersions', fn() =>
                $this->routeVersions->first() ? [
                    'id'           => $this->routeVersions->first()->id,
                    'source_api'   => $this->routeVersions->first()->source_api,
                    'distance_km'  => $this->routeVersions->first()->distance_km,
                    'duration_min' => $this->routeVersions->first()->duration_min,
                    'calculated_at'=> $this->routeVersions->first()->calculated_at?->toISOString(),
                ] : null
            ),
            'created_at'       => $this->created_at?->toISOString(),
        ];
    }
}
