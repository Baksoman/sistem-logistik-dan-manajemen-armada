<?php

namespace App\Services;

use App\Models\Vehicle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VehicleService
{
    public function getPaginatedVehicles(int $perPage = 10): LengthAwarePaginator
    {
        return Vehicle::with('vehicleType')->latest()->paginate($perPage);
    }

    public function getAllVehicles()
    {
        return Vehicle::orderBy('plate_number')->get();
    }

    public function getVehicleTypes()
    {
        return \App\Models\VehicleType::all();
    }

    public function createVehicle(array $data): Vehicle
    {
        return Vehicle::create($data);
    }

    public function updateVehicle(Vehicle $vehicle, array $data): Vehicle
    {
        $vehicle->update($data);
        return $vehicle;
    }

    public function deleteVehicle(Vehicle $vehicle): void
    {
        if ($vehicle->status === 'on_trip' || $vehicle->status === 'active') {
            throw new \Exception('Pastikan kendaraan berstatus Out of Service sebelum dihapus dari sistem (safety lock).');
        }
        $vehicle->delete();
    }
}
