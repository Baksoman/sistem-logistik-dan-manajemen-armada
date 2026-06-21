<?php

namespace App\Http\Controllers;

use App\Models\DriverProfile;
use App\Services\DriverProfileService;
use App\Http\Requests\Driver\StoreDriverProfileRequest;
use App\Http\Requests\Driver\UpdateDriverProfileRequest;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\DriverExport;

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

    public function exportExcel()
    {
        return Excel::download(new DriverExport, 'laporan-drivers-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function exportPdf()
    {
        $drivers = DriverProfile::with('user')->latest()->get();
        $headings = ['ID', 'Name', 'Email', 'License Number', 'License Type', 'Status', 'Assignment', 'Phone', 'Created At'];
        $data = $drivers->map(fn($d) => [
            $d->id,
            $d->user->name ?? '-',
            $d->user->email ?? '-',
            $d->license_number,
            $d->license_type,
            $d->status,
            $d->assigned_vehicle_id ? 'Assigned' : 'Unassigned',
            $d->phone ?? '-',
            $d->created_at?->format('Y-m-d H:i') ?? ($d->joined_at?->format('Y-m-d H:i') ?? '-'),
        ]);

        $pdf = Pdf::loadView('reports.pdf', [
            'title' => 'Laporan Data Supir',
            'headings' => $headings,
            'data' => $data,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-drivers-' . now()->format('Ymd-His') . '.pdf');
    }
}
