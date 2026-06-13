<?php

namespace App\Http\Controllers;

use App\Services\VehicleMaintenanceService;
use App\Services\VehicleService;
use App\Models\VehicleMaintenance;
use App\Http\Requests\Fleet\StoreVehicleMaintenanceRequest;
use App\Http\Requests\Fleet\UpdateVehicleMaintenanceRequest;

class VehicleMaintenanceController extends Controller
{
    public function __construct(
        protected VehicleMaintenanceService $maintenanceService,
        protected VehicleService $vehicleService
    ) {}

    public function index()
    {
        $maintenances = $this->maintenanceService->getPaginatedMaintenances();
        $vehicles = $this->vehicleService->getAllVehicles();
        
        return view('fleet.maintenances.index', compact('maintenances', 'vehicles'));
    }

    public function store(StoreVehicleMaintenanceRequest $request)
    {
        $this->maintenanceService->createMaintenance($request->validated());
        return back()->with('success', 'Vehicle maintenance record created successfully.');
    }

    public function update(UpdateVehicleMaintenanceRequest $request, VehicleMaintenance $maintenance)
    {
        $this->maintenanceService->updateMaintenance($maintenance, $request->validated());
        return back()->with('success', 'Vehicle maintenance record updated successfully.');
    }
}
