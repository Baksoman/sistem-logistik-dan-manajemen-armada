<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'shipment_number'   => $this->shipment_number,
            'status'            => $this->status,
            'sla_status'        => $this->sla_status, // Computed attribute on model
            'total_distance_km' => $this->total_distance_km,
            'total_cost'        => $this->total_cost,
            'cost_per_km'       => $this->cost_per_km,
            'sla_target_at'     => $this->sla_target_at?->toISOString(),
            'started_at'        => $this->started_at?->toISOString(),
            'completed_at'      => $this->completed_at?->toISOString(),
            'driver'            => $this->whenLoaded('driver', fn() => [
                'id'           => $this->driver->id,
                'name'         => $this->driver->user?->name,
                'phone'        => $this->driver->phone,
                'license_type' => $this->driver->license_type,
                'status'       => $this->driver->status,
            ]),
            'vehicle'           => $this->whenLoaded('vehicle', fn() => [
                'id'           => $this->vehicle->id,
                'plate_number' => $this->vehicle->plate_number,
                'brand'        => $this->vehicle->brand,
                'model'        => $this->vehicle->model,
                'type'         => $this->vehicle->vehicleType?->name ?? null,
            ]),
            'orders_count'      => $this->whenLoaded('orders', fn() => $this->orders->count()),
            'route'             => $this->whenLoaded('routeVersion', fn() => $this->routeVersion ? [
                'version_id'  => $this->routeVersion->id,
                'source_api'  => $this->routeVersion->source_api,
                'distance_km' => $this->routeVersion->distance_km,
                'duration_min'=> $this->routeVersion->duration_min,
                'route'       => $this->routeVersion->relationLoaded('route') ? [
                    'id'               => $this->routeVersion->route->id,
                    'route_code'       => $this->routeVersion->route->route_code,
                    'origin_name'      => $this->routeVersion->route->origin_name,
                    'destination_name' => $this->routeVersion->route->destination_name,
                ] : null,
            ] : null),
            'created_at'        => $this->created_at?->toISOString(),
        ];
    }
}
