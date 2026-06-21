<?php

namespace App\Exports\Warehouse;

use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InboundExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return StockMovement::with(['stockItem.warehouse', 'stockItem.unitType', 'creator'])
            ->where('type', 'inbound')
            ->latest()
            ->get()
            ->map(function ($movement) {
                return [
                    $movement->id,
                    $movement->reference_number ?? '-',
                    $movement->stockItem->sku ?? '-',
                    $movement->stockItem->name ?? '-',
                    $movement->stockItem->warehouse->name ?? '-',
                    $movement->quantity,
                    $movement->stockItem->unitType->name ?? 'pcs',
                    $movement->creator->name ?? '-',
                    $movement->notes ?? '-',
                    $movement->created_at?->format('Y-m-d H:i') ?? '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID', 'Reference Number', 'SKU', 'Item Name',
            'Warehouse', 'Quantity', 'Unit',
            'Received By', 'Notes', 'Date',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
