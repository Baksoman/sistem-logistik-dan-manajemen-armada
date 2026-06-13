<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Services\VehicleService;
use App\Http\Requests\Vehicle\StoreVehicleRequest;
use App\Http\Requests\Vehicle\UpdateVehicleRequest;

class VehicleController extends Controller
{
    public function __construct(protected VehicleService $vehicleService) {}

    public function index()
    {
        $vehicles = $this->vehicleService->getPaginatedVehicles(10);
        $vehicleTypes = $this->vehicleService->getVehicleTypes();
        return view('fleet.index', compact('vehicles', 'vehicleTypes'));
    }

    public function store(StoreVehicleRequest $request)
    {
        $this->vehicleService->createVehicle($request->validated());
        return back()->with('success', 'Vehicle registered successfully.');
    }

    public function update(UpdateVehicleRequest $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $this->vehicleService->updateVehicle($vehicle, $request->validated());
        return back()->with('success', 'Vehicle updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);
            $this->vehicleService->deleteVehicle($vehicle);
            return back()->with('success', 'Vehicle deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
