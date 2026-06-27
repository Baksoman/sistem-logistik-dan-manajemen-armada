<?php

namespace App\Http\Requests\Search;

use Illuminate\Foundation\Http\FormRequest;

class ShipmentSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'           => 'nullable|string|max:100',
            'status'           => 'nullable|string|in:Pending,On Process,Delivered,Failed',
            'driver_id'        => 'nullable|uuid|exists:driver_profiles,id',
            'vehicle_id'       => 'nullable|uuid|exists:vehicles,id',
            'route_version_id' => 'nullable|uuid|exists:route_versions,id',
            'started_from'     => 'nullable|date',
            'started_to'       => 'nullable|date|after_or_equal:started_from',
            'sla_status'       => 'nullable|string|in:late,on_time,at_risk',
            'per_page'         => 'nullable|integer|min:5|max:100',
            'page'             => 'nullable|integer|min:1',
        ];
    }
}
