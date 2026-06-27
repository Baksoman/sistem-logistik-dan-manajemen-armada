<?php

namespace App\Http\Requests\Search;

use Illuminate\Foundation\Http\FormRequest;

class VehicleSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'          => 'nullable|string|max:100',
            'vehicle_type_id' => 'nullable|uuid|exists:vehicle_types,id',
            'status'          => 'nullable|string|in:available,on_trip,maintenance,inactive',
            'fuel_type'       => 'nullable|string|max:50',
            'capacity_min'    => 'nullable|numeric|min:0',
            'capacity_max'    => 'nullable|numeric|min:0|gte:capacity_min',
            'per_page'        => 'nullable|integer|min:5|max:100',
            'page'            => 'nullable|integer|min:1',
        ];
    }
}
