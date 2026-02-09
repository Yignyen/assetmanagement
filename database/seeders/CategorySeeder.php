<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Laptops', 'description' => 'Office and staff laptops'],
            ['name' => 'Desktop Computers', 'description' => 'Office desktop systems'],
            ['name' => 'Monitors', 'description' => 'LCD / LED monitors'],
            ['name' => 'Printers', 'description' => 'Office printers'],
            ['name' => 'Networking Equipment', 'description' => 'Routers and switches'],
            ['name' => 'Servers', 'description' => 'Physical servers'],
            ['name' => 'UPS & Power Backup', 'description' => 'UPS and power devices'],
            ['name' => 'Computer Lab Systems', 'description' => 'Student lab computers'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name'], 'type' => 'asset'],
                ['description' => $category['description']]
            );
        }
    }
}
