<?php

namespace App\Exports\Warehouse;

use App\Models\StockItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return StockItem::with(['warehouse', 'category', 'unitType', 'zone', 'rack'])
            ->get()
            ->map(function ($item) {
                return [
                    $item->id,
                    $item->sku,
                    $item->name,
                    $item->brand ?? '-',
                    $item->category->name ?? '-',
                    $item->warehouse->name ?? '-',
                    $item->zone->name ?? '-',
                    $item->rack->name ?? '-',
                    $item->quantity,
                    $item->allocated_quantity ?? 0,
                    $item->quantity - ($item->allocated_quantity ?? 0),
                    $item->unitType->name ?? '-',
                    $item->weight_kg,
                    $item->volume_cbm,
                    $item->created_at?->format('Y-m-d') ?? '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID', 'SKU', 'Name', 'Brand', 'Category',
            'Warehouse', 'Zone', 'Rack',
            'Qty Total', 'Qty Allocated', 'Qty Available',
            'Unit', 'Weight (kg)', 'Volume (m³)', 'Created At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
