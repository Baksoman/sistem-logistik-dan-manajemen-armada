<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ItemCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Elektronik', 'Furniture', 'Makanan', 'Material Bangunan', 'Otomotif'];

        foreach ($categories as $category) {
            DB::table('item_categories')->insert([
                'id' => Str::uuid(),
                'name' => $category,
            ]);
        }
    }
}
