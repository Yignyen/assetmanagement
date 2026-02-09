<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssetModel;
use App\Models\Asset;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1️⃣ Master data first
        $this->call([
            DepartmentSeeder::class,
            UserSeeder::class,
            CategorySeeder::class, // ✅ FIXED categories (Laptops, Monitors, etc.)
        ]);

        // 2️⃣ Asset models (office-style fake data)
        AssetModel::factory()
            ->count(12)
            ->create();

        // 3️⃣ Assets (office assets)
        Asset::factory()
            ->count(40)
            ->create(); // name auto-generated in model
    }
}
