<?php

namespace App\Http\Requests\Fleet;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => 'required|uuid|exists:vehicles,id',
            'maintenance_type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'status' => 'required|in:Scheduled,In Progress,Completed',
            'scheduled_date' => 'required|date',
            'completed_date' => 'nullable|date|after_or_equal:scheduled_date',
            'next_maintenance_date' => 'nullable|date|after:scheduled_date',
        ];
    }
}
