<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_type_id' => 'required|exists:vehicle_types,id', 
            'plate_number' => 'required|string|unique:vehicles',
            'brand' => 'required|string',
            'model' => 'required|string',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'capacity_kg' => 'required|numeric|min:0',
            'capacity_volume_cbm' => 'required|numeric|min:0',
            'fuel_type' => 'required|string',
            'status' => 'required|in:available,inactive',
            'kir_expired_at' => 'required|date',
            'stnk_expired_at' => 'required|date',
        ];
    }
}
