<?php

namespace Database\Seeders;

use App\Models\Asset;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Asset::create([
        'name' => 'Dell Latitude 5400',
        'serial_no' => 'SN123456',
        'asset_tag' => 'ASSET001',
        'status' => 'available',
        'category_id' => 1,
        'purchase_date' => '2023-08-10',
    ]);

    Asset::create([
        'name' => 'HP Pavilion',
        'serial_no' => 'SN654321',
        'asset_tag' => 'ASSET002',
        'status' => 'available',
        'category_id' => 1,
        'purchase_date' => '2024-01-15',
    ]);
    }
}
