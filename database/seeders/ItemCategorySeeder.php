<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ItemCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Bahan Baku', 'description' => 'Material mentah untuk proses produksi'],
            ['name' => 'Barang Setengah Jadi', 'description' => 'Produk yang masih dalam proses pengerjaan'],
            ['name' => 'Barang Jadi', 'description' => 'Produk akhir siap distribusi'],

            ['name' => 'Elektronik', 'description' => 'Perangkat dan komponen elektronik'],
            ['name' => 'Komputer & Aksesoris', 'description' => 'PC, laptop, monitor, keyboard, mouse, dll'],
            ['name' => 'Peralatan Jaringan', 'description' => 'Router, switch, kabel jaringan, access point'],

            ['name' => 'Mesin & Peralatan Industri', 'description' => 'Mesin produksi dan peralatan berat'],
            ['name' => 'Suku Cadang', 'description' => 'Spare part kendaraan dan mesin'],
            ['name' => 'Peralatan Bengkel', 'description' => 'Tools dan peralatan perbaikan'],

            ['name' => 'Material Bangunan', 'description' => 'Semen, besi, bata, dan material konstruksi'],
            ['name' => 'Peralatan Listrik', 'description' => 'Kabel, saklar, MCB, dan instalasi kelistrikan'],
            ['name' => 'Peralatan Plumbing', 'description' => 'Pipa, fitting, valve, dan perlengkapan air'],

            ['name' => 'Furniture', 'description' => 'Meja, kursi, lemari, dan perabotan'],
            ['name' => 'Peralatan Kantor', 'description' => 'Printer, scanner, dan peralatan operasional kantor'],
            ['name' => 'Alat Tulis Kantor (ATK)', 'description' => 'Kertas, pulpen, map, dan perlengkapan tulis'],

            ['name' => 'Kemasan & Packaging', 'description' => 'Kardus, plastik wrap, bubble wrap, dan label'],
            ['name' => 'Perlengkapan Gudang', 'description' => 'Pallet, rak, trolley, dan peralatan gudang'],

            ['name' => 'Alat Kebersihan', 'description' => 'Sapu, pel, cairan pembersih, dan perlengkapan kebersihan'],
            ['name' => 'Alat Keamanan', 'description' => 'CCTV, alarm, gembok, dan peralatan keamanan'],
            ['name' => 'Alat Pelindung Diri (APD)', 'description' => 'Helm, masker, sarung tangan, dan kacamata safety'],

            ['name' => 'Makanan', 'description' => 'Produk makanan kemasan dan bahan makanan'],
            ['name' => 'Minuman', 'description' => 'Produk minuman kemasan'],

            ['name' => 'Obat & Peralatan Medis', 'description' => 'P3K, obat-obatan, dan peralatan kesehatan'],

            ['name' => 'Pakaian & Seragam', 'description' => 'Seragam kerja, jas lab, dan pakaian operasional'],

            ['name' => 'Bahan Kimia', 'description' => 'Cairan kimia untuk industri dan laboratorium'],
            ['name' => 'Pelumas & Oli', 'description' => 'Oli mesin, grease, dan pelumas industri'],
            ['name' => 'Bahan Bakar', 'description' => 'Solar, bensin, dan gas untuk operasional'],

            ['name' => 'Aset Tetap', 'description' => 'Barang inventaris bernilai tinggi dan jangka panjang'],
            ['name' => 'Barang Konsumsi', 'description' => 'Barang habis pakai untuk operasional harian'],

            ['name' => 'Lain-lain', 'description' => 'Kategori untuk barang yang belum terklasifikasi'],
        ];

        foreach ($categories as $category) {
            DB::table('item_categories')->insert([
                'id' => Str::uuid(),
                'name' => $category['name'],
                'description' => $category['description'],
            ]);
        }
    }
}
