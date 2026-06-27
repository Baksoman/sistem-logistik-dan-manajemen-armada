<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference_number' => $this->reference_number,
            'type' => $this->type,
            'quantity' => $this->quantity,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'stock_item' => [
                'id' => $this->stockItem?->id,
                'name' => $this->stockItem?->name,
                'sku' => $this->stockItem?->sku,
                'warehouse' => [
                    'id' => $this->stockItem?->warehouse?->id,
                    'name' => $this->stockItem?->warehouse?->name,
                ]
            ],
            'creator' => [
                'id' => $this->creator?->id,
                'name' => $this->creator?->name,
            ]
        ];
    }
}
