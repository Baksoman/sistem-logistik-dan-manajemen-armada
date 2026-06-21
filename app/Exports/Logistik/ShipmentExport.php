<?php

namespace App\Exports\Logistik;

use App\Models\Shipment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShipmentExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Shipment::with(['driver.user', 'vehicle.vehicleType', 'routeVersion.route'])
            ->latest()
            ->get()
            ->map(function ($shipment) {
                return [
                    $shipment->id,
                    $shipment->shipment_number,
                    $shipment->driver->user->name ?? '-',
                    $shipment->vehicle->plate_number ?? '-',
                    $shipment->vehicle->vehicleType->name ?? '-',
                    $shipment->routeVersion->route->route_code ?? '-',
                    $shipment->total_distance_km ?? '-',
                    $shipment->total_cost ? number_format($shipment->total_cost, 0, ',', '.') : '-',
                    $shipment->status,
                    $shipment->sla_status,
                    $shipment->started_at?->format('Y-m-d H:i') ?? '-',
                    $shipment->completed_at?->format('Y-m-d H:i') ?? '-',
                    $shipment->sla_target_at?->format('Y-m-d H:i') ?? '-',
                    $shipment->created_at?->format('Y-m-d H:i') ?? '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID', 'Shipment Number', 'Driver', 'Vehicle Plate', 'Vehicle Type',
            'Route', 'Distance (km)', 'Total Cost (IDR)',
            'Status', 'SLA Status',
            'Started At', 'Completed At', 'SLA Target', 'Created At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
