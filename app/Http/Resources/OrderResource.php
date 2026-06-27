<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'order_number'         => $this->order_number,
            'status'               => $this->status,
            'tracking_status'      => $this->tracking_status,
            'destination_address'  => $this->destination_address,
            'total_weight'         => $this->total_weight,
            'total_volume'         => $this->total_volume,
            'quoted_price'         => $this->quoted_price,
            'customer'             => $this->whenLoaded('customer', fn() => [
                'id'           => $this->customer->id,
                'company_name' => $this->customer->company_name,
                'contact'      => $this->customer->contact_person,
            ]),
            'origin_warehouse'     => $this->whenLoaded('originWarehouse', fn() => [
                'id'   => $this->originWarehouse->id,
                'name' => $this->originWarehouse->name,
                'code' => $this->originWarehouse->code,
            ]),
            'current_warehouse'    => $this->whenLoaded('currentWarehouse', fn() => [
                'id'   => $this->currentWarehouse->id,
                'name' => $this->currentWarehouse->name,
                'code' => $this->currentWarehouse->code,
            ]),
            'created_by'           => $this->whenLoaded('creator', fn() => [
                'id'   => $this->creator->id,
                'name' => $this->creator->name,
            ]),
            'created_at'           => $this->created_at?->toISOString(),
            'updated_at'           => $this->updated_at?->toISOString(),
        ];
    }
}
