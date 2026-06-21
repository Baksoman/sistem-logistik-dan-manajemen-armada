<?php

namespace App\Exports\Logistik;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Order::with(['customer', 'originWarehouse', 'currentWarehouse', 'creator'])
            ->latest()
            ->get()
            ->map(function ($order) {
                return [
                    $order->id,
                    $order->order_number,
                    $order->customer->company_name ?? '-',
                    $order->customer->email ?? '-',
                    $order->originWarehouse->name ?? '-',
                    $order->destination_address,
                    $order->total_weight,
                    $order->total_volume,
                    $order->quoted_price ? number_format($order->quoted_price, 0, ',', '.') : '-',
                    $order->estimated_distance_km ?? '-',
                    $order->status,
                    $order->tracking_status?->value ?? '-',
                    $order->creator->name ?? '-',
                    $order->created_at?->format('Y-m-d H:i') ?? '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID', 'Order Number', 'Customer', 'Customer Email',
            'Origin Warehouse', 'Destination Address',
            'Total Weight (kg)', 'Total Volume (m³)',
            'Quoted Price (IDR)', 'Est. Distance (km)',
            'Status', 'Tracking Status', 'Created By', 'Created At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
