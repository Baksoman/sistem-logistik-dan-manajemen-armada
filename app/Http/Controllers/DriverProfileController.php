<?php

namespace App\Http\Controllers;

use App\Models\DriverProfile;
use App\Services\DriverProfileService;
use App\Http\Requests\Driver\StoreDriverProfileRequest;
use App\Http\Requests\Driver\UpdateDriverProfileRequest;

class DriverProfileController extends Controller
{
    public function __construct(protected DriverProfileService $driverProfileService) {}

    public function index()
    {
        $drivers = $this->driverProfileService->getPaginatedDrivers(10);
        $users = $this->driverProfileService->getAvailableUsersForDriver();
        
        return view('drivers.index', compact('drivers', 'users'));
    }

    public function store(StoreDriverProfileRequest $request)
    {
        $this->driverProfileService->createDriverProfile($request->validated());
        return back()->with('success', 'Driver profile created successfully.');
    }

    public function update(UpdateDriverProfileRequest $request, $id)
    {
        $driver = DriverProfile::findOrFail($id);
        $this->driverProfileService->updateDriverProfile($driver, $request->validated());
        return back()->with('success', 'Driver profile updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $driver = DriverProfile::findOrFail($id);
            $this->driverProfileService->deleteDriverProfile($driver);
            return back()->with('success', 'Driver profile deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
