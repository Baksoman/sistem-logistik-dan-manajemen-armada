<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDriverProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $driverId = $this->route('driver');
        return [
            'user_id' => 'required|exists:users,id',
            'nik' => 'required|string|unique:driver_profiles,nik,' . $driverId,
            'phone' => 'required|string',
            'address' => 'required|string',
            'license_number' => 'required|string|unique:driver_profiles,license_number,' . $driverId,
            'license_type' => 'required|in:A,B1,B2',
            'license_expired_at' => 'required|date',
            'status' => 'required|in:available,on_trip,inactive',
            'joined_at' => 'required|date',
        ];
    }
}
