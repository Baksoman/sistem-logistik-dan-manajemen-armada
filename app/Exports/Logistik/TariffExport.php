<?php

namespace App\Exports\Logistik;

use App\Models\Tariff;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TariffExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Tariff::with(['route', 'vehicleType'])->latest()->get()->map(function ($tariff) {
            return [
                $tariff->id,
                $tariff->route ? ($tariff->route->origin_name . ' - ' . $tariff->route->destination_name) : 'All Routes',
                $tariff->vehicleType->name ?? 'All Vehicles',
                $tariff->price_per_km,
                $tariff->price_per_kg,
                $tariff->price_per_cbm,
                $tariff->fixed_price,
                $tariff->is_active ? 'Active' : 'Inactive',
                $tariff->created_at?->format('Y-m-d H:i') ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID', 'Route', 'Vehicle Type', 'Price/km (IDR)', 'Price/kg (IDR)',
            'Price/cbm (IDR)', 'Fixed Price (IDR)', 'Status', 'Created At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
