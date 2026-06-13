<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VehicleMaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil beberapa armada secara acak untuk di-seeding
        $vehicles = Vehicle::take(2)->get();

        if ($vehicles->count() < 2) {
            return; // Skip jika belum ada data fleet
        }

        $maintenances = [
            [
                'id' => Str::uuid(),
                'vehicle_id' => $vehicles[0]->id,
                'maintenance_type' => 'Servis Berkala & Ganti Oli',
                'description' => 'Ganti oli mesin, cek filter udara, dan cek kampas rem.',
                'cost' => 1500000,
                'status' => 'Completed',
                'scheduled_date' => Carbon::now()->subMonths(1)->format('Y-m-d'),
                'completed_date' => Carbon::now()->subMonths(1)->addDays(2)->format('Y-m-d'),
                'next_maintenance_date' => Carbon::now()->addMonths(5)->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'vehicle_id' => $vehicles[1]->id,
                'maintenance_type' => 'Perpanjangan KIR',
                'description' => 'Uji emisi dan perpanjangan dokumen KIR tahunan.',
                'cost' => 850000,
                'status' => 'Scheduled',
                'scheduled_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
                'completed_date' => null,
                'next_maintenance_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('vehicle_maintenances')->insert($maintenances);
    }
}