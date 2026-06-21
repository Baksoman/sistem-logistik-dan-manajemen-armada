<?php

namespace App\Exports;

use App\Models\Vehicle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VehiclesExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Vehicle::with(['vehicleType'])
            ->latest()
            ->get()
            ->map(function ($vehicle) {
                return [
                    $vehicle->id,
                    $vehicle->plate_number,
                    $vehicle->brand,
                    $vehicle->model,
                    $vehicle->year,
                    $vehicle->vehicleType->name ?? '-',
                    $vehicle->capacity_kg,
                    $vehicle->capacity_volume_cbm,
                    $vehicle->fuel_type,
                    $vehicle->status,
                    $vehicle->kir_expired_at?->format('Y-m-d') ?? '-',
                    $vehicle->stnk_expired_at?->format('Y-m-d') ?? '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID', 'Plate Number', 'Brand', 'Model', 'Year',
            'Vehicle Type', 'Capacity (kg)', 'Capacity (m³)',
            'Fuel Type', 'Status',
            'KIR Expired', 'STNK Expired', 'Created At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
