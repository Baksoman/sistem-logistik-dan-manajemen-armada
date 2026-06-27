<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'nik'                 => $this->nik,
            'phone'               => $this->phone,
            'license_number'      => $this->license_number,
            'license_type'        => $this->license_type,
            'license_expired_at'  => $this->license_expired_at?->toDateString(),
            'rating'              => $this->rating,
            'status'              => $this->status,
            'joined_at'           => $this->joined_at?->toDateString(),
            // Merge the User fields at the top level for convenience.
            // The frontend should not need to know about the DriverProfile/User split.
            'user'                => $this->whenLoaded('user', fn() => [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
            ]),
        ];
    }
}
