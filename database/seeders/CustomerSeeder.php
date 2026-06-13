<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'id' => Str::uuid(),
                'code' => 'CUST-001',
                'company_name' => 'PT Maju Bersama',
                'contact_person' => 'Bapak Anwar',
                'phone' => '08122334455',
                'email' => 'contact@majubersama.co.id',
                'address' => 'Jl. Panglima Sudirman No. 10, Surabaya',
                'latitude' => -7.2687,
                'longitude' => 112.7423,
            ],
            [
                'id' => Str::uuid(),
                'code' => 'CUST-002',
                'company_name' => 'CV Sumber Makmur',
                'contact_person' => 'Ibu Rina',
                'phone' => '08199887766',
                'email' => 'info@sumbermakmur.com',
                'address' => 'Jl. Basuki Rahmat No. 25, Sidoarjo',
                'latitude' => -7.4478,
                'longitude' => 112.7183,
            ]
        ];

        DB::table('customers')->insert($customers);
    }
}
