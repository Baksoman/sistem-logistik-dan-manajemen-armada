<?php

namespace App\Services;

use App\Models\VehicleMaintenance;
use Illuminate\Pagination\LengthAwarePaginator;

class VehicleMaintenanceService
{
    public function getPaginatedMaintenances(int $perPage = 10): LengthAwarePaginator
    {
        return VehicleMaintenance::with('vehicle')->latest('scheduled_date')->paginate($perPage);
    }

    public function createMaintenance(array $data): VehicleMaintenance
    {
        $maintenance = VehicleMaintenance::create($data);
        $this->syncVehicleStatus($maintenance);
        return $maintenance;
    }

    public function updateMaintenance(VehicleMaintenance $maintenance, array $data): VehicleMaintenance
    {
        $maintenance->update($data);
        $this->syncVehicleStatus($maintenance);
        return $maintenance;
    }

    protected function syncVehicleStatus(VehicleMaintenance $maintenance): void
    {
        $vehicle = $maintenance->vehicle;
        
        if ($maintenance->status === 'In Progress') {
            $vehicle->update(['status' => 'maintenance']);
        } elseif ($maintenance->status === 'Completed') {
            $hasOtherInProgress = VehicleMaintenance::where('vehicle_id', $vehicle->id)
                ->where('status', 'In Progress')
                ->where('id', '!=', $maintenance->id)
                ->exists();

            if (!$hasOtherInProgress && $vehicle->status === 'maintenance') {
                $vehicle->update(['status' => 'available']);
            }
        }
    }
}
