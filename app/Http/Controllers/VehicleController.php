<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Services\VehicleService;
use App\Http\Requests\Vehicle\StoreVehicleRequest;
use App\Http\Requests\Vehicle\UpdateVehicleRequest;
use App\Exports\VehiclesExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

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

    public function exportExcel()
    {
        return Excel::download(new VehiclesExport, 'laporan-armada-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function exportPdf()
    {
        $vehicles = Vehicle::with(['vehicleType'])->latest()->get();
        $headings = ['ID', 'Plate Number', 'Brand', 'Model', 'Year', 'Type', 'Capacity (kg)', 'Capacity (m³)', 'Fuel Type', 'Status', 'KIR Expired', 'STNK Expired'];
        $data = $vehicles->map(fn($v) => [
            $v->id, $v->plate_number, $v->brand, $v->model, $v->year,
            $v->vehicleType->name ?? '-',
            $v->capacity_kg, $v->capacity_volume_cbm,
            $v->fuel_type, $v->status,
            $v->kir_expired_at ? \Carbon\Carbon::parse($v->kir_expired_at)->format('Y-m-d') : '-',
            $v->stnk_expired_at ? \Carbon\Carbon::parse($v->stnk_expired_at)->format('Y-m-d') : '-',
        ]);

        $pdf = Pdf::loadView('reports.pdf', [
            'title' => 'Laporan Data Armada Kendaraan',
            'headings' => $headings,
            'data' => $data,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-armada-' . now()->format('Ymd-His') . '.pdf');
    }
}
