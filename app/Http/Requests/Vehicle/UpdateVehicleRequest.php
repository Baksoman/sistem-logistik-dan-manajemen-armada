<?php

namespace App\Http\Requests\Vehicle;

use App\Models\VehicleMaintenance;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehicleId = $this->route('vehicle');
        return [
            'vehicle_type_id' => 'required|exists:vehicle_types,id', 
            'plate_number' => 'required|string|unique:vehicles,plate_number,' . $vehicleId,
            'brand' => 'required|string',
            'model' => 'required|string',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'capacity_kg' => 'required|numeric|min:0',
            'capacity_volume_cbm' => 'required|numeric|min:0',
            'fuel_cost_per_km' => 'required|numeric|min:0',
            'fuel_type' => 'required|string',
            'status' => 'required|in:available,maintenance,on_trip,inactive',
            'kir_expired_at' => 'required|date',
            'stnk_expired_at' => 'required|date',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $status = $this->input('status');
            $vehicleId = $this->route('vehicle');
            
            if (!$vehicleId) return;

            $hasInProgress = VehicleMaintenance::where('vehicle_id', $vehicleId)
                ->where('status', 'In Progress')
                ->exists();

            if ($status === 'maintenance' && !$hasInProgress) {
                $validator->errors()->add('status', 'Kendaraan tidak dapat diubah ke Maintenance. Silakan buat catatan Maintenance dengan status In Progress terlebih dahulu.');
            }

            if ($status !== 'maintenance' && $hasInProgress) {
                $validator->errors()->add('status', 'Kendaraan sedang dalam proses Maintenance. Anda harus menyelesaikan catatan Maintenance terlebih dahulu.');
            }
        });
    }
}
