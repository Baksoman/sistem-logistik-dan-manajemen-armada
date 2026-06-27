<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'plate_number'        => $this->plate_number,
            'brand'               => $this->brand,
            'model'               => $this->model,
            'year'                => $this->year,
            'status'              => $this->status,
            'fuel_type'           => $this->fuel_type,
            'fuel_cost_per_km'    => $this->fuel_cost_per_km,
            'capacity_kg'         => $this->capacity_kg,
            'capacity_volume_cbm' => $this->capacity_volume_cbm,
            'kir_expired_at'      => $this->kir_expired_at?->toDateString(),
            'stnk_expired_at'     => $this->stnk_expired_at?->toDateString(),
            'vehicle_type'        => $this->whenLoaded('vehicleType', fn() => [
                'id'   => $this->vehicleType->id,
                'name' => $this->vehicleType->name,
            ]),
            'created_at'          => $this->created_at?->toISOString(),
        ];
    }
}
