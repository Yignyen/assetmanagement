<?php

namespace Database\Seeders;

use App\Models\Category;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [

            // ===== ASSETS =====
            ['name' => 'Laptop',   'type' => 'asset',     'description' => 'Portable computers'],
            ['name' => 'Desktop',  'type' => 'asset',     'description' => 'Desktop computers'],
            ['name' => 'Display',  'type' => 'asset',     'description' => 'Displays and screens'],
            ['name' => 'Printer',  'type' => 'asset',     'description' => 'Office printers'],
            ['name' => 'Router',   'type' => 'asset',     'description' => 'Network devices'],
            ['name' => 'furniture',   'type' => 'asset',     'description' => 'furniture'],

            // ===== ACCESSORIES =====
            ['name' => 'Mouse',     'type' => 'accessory', 'description' => 'Computer mouse'],
            ['name' => 'Keyboard',  'type' => 'accessory', 'description' => 'Keyboards'],
            ['name' => 'Cable',     'type' => 'accessory', 'description' => 'All types of cables'],
            ['name' => 'Adapter',   'type' => 'accessory', 'description' => 'USB / HDMI / Power adapters'],
            ['name' => 'Charger',   'type' => 'accessory', 'description' => 'Laptop and phone chargers'],
            ['name' => 'Pendrive',  'type' => 'accessory', 'description' => 'USB storage devices'],
            ['name' => 'Combo',  'type' => 'accessory', 'description' => 'Mosue and keyboard'],
            ['name' => 'Dock/Adapter',  'type' => 'accessory', 'description' => 'docs and adapt'],
            ['name' => 'External Accessory',  'type' => 'accessory', 'description' => 'docs and adapt'],

            // ===== COMPONENTS =====
            ['name' => 'SSD', 'type' => 'component', 'description' => 'Solid state drives'],
            ['name' => 'HDD', 'type' => 'component', 'description' => 'Hard disk drives'],
            ['name' => 'RAM', 'type' => 'component', 'description' => 'Memory modules'],
            ['name' => 'GPU', 'type' => 'component', 'description' => 'Graphics cards'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}