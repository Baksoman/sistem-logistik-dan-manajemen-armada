<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CostCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Fuel', 'Toll', 'Parking', 'Maintenance', 'Driver Meals', 'Ferry Ticket'];

        foreach ($categories as $category) {
            DB::table('cost_categories')->insert([
                'id' => Str::uuid(),
                'name' => $category,
            ]);
        }
    }
}
