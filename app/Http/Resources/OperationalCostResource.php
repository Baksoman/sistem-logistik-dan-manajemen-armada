<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OperationalCostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'shipment' => [
                'id' => $this->shipment->id ?? null,
                'shipment_number' => $this->shipment->shipment_number ?? null,
                'status' => $this->shipment->status ?? null,
            ],
            'driver' => [
                'name' => $this->shipment->driver->user->name ?? 'Unknown',
            ],
            'category' => [
                'id' => $this->category->id ?? null,
                'name' => $this->category->name ?? 'Unknown',
            ],
            'amount' => $this->amount,
            'description' => $this->description,
            'receipt_path' => $this->receipt_path ? asset('storage/' . $this->receipt_path) : null,
            'recorded_at' => $this->recorded_at ? $this->recorded_at->format('Y-m-d H:i') : null,
            'created_at' => $this->created_at,
        ];
    }
}
