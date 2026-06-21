<?php

namespace App\Exports;

use App\Models\DriverProfile;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DriverExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return DriverProfile::with('user')->latest()->get()->map(function ($driver) {
            return [
                $driver->id,
                $driver->user->name ?? '-',
                $driver->user->email ?? '-',
                $driver->license_number,
                $driver->license_type,
                $driver->status,
                $driver->assigned_vehicle_id ? 'Assigned' : 'Unassigned',
                $driver->phone ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID', 'Name', 'Email', 'License Number', 'License Type',
            'Status', 'Assignment', 'Phone', 'Created At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
