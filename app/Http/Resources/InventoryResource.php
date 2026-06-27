<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'upc' => $this->upc,
            'brand' => $this->brand,
            'name' => $this->name,
            'warehouse' => [
                'id' => $this->warehouse?->id,
                'name' => $this->warehouse?->name,
            ],
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ],
            'zone' => [
                'id' => $this->zone?->id,
                'name' => $this->zone?->name,
            ],
            'rack' => [
                'id' => $this->rack?->id,
                'name' => $this->rack?->name,
            ],
            'unit_type' => [
                'id' => $this->unitType?->id,
                'name' => $this->unitType?->name,
            ],
            'quantity' => $this->quantity,
            'allocated_quantity' => $this->allocated_quantity,
            'min_quantity' => $this->min_quantity,
            'is_low_stock' => $this->quantity <= $this->min_quantity,
            'weight_kg' => $this->weight_kg,
            'volume_cbm' => $this->volume_cbm,
            'created_at' => $this->created_at,
        ];
    }
}
