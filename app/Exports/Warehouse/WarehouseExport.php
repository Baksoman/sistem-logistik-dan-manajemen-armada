<?php

namespace App\Exports\Warehouse;

use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WarehouseExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Warehouse::with('users')->withCount(['zones', 'racks', 'stockItems'])
            ->latest()->get()->map(function ($warehouse) {
                return [
                    $warehouse->name,
                    $warehouse->code,
                    $warehouse->address,
                    $warehouse->users->pluck('name')->join(', ') ?: '-',
                    $warehouse->is_active ? 'Active' : 'Inactive',
                    $warehouse->zones_count,
                    $warehouse->racks_count,
                    $warehouse->stock_items_count,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Name', 'Code', 'Address', 'Assigned Staff', 'Status', 'Zones', 'Racks', 'Stock Items',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
