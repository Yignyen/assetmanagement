<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Category;
use App\Models\Accessory;

class AccessorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $items = [
            ['Keyboard', 'Wired Keyboard', 1],
            ['Keyboard', 'Dell Wired Keyboard', 1],
            ['Mouse', 'Dell Wired Mouse', 2],
            ['Combo', 'Wired Keyboard and Mouse', 1],
            ['Combo', 'Wireless Keyboard and Mouse', 2],
            ['Combo', 'Dell Wireless Keyboard and Mouse', 5],
            ['Dock/Adapter', 'USB-C Docking Station', 4],
            ['Dock/Adapter', 'USB-C Mobile Adapter', 2],
            ['Cable', 'Thunderbolt for Mac', 1],
            ['Cable', 'Thunderbolt to Ethernet Cable', 1],
            ['External Accessory', 'Pendrive 8GB', 1],
        ];

        foreach ($items as [$categoryName, $name, $qty]) {
            $category = Category::where('name', $categoryName)
                ->where('type', 'accessory')
                ->first();
            /* if (!$category) {
                throw new \Exception("Missing asset category: {$categoryName}");
            } */

            Accessory::create([
                'name' => $name,
                'total_qty' => $qty,
                'available_qty' => $qty,
                'category_id' => $category->id,
            ]);
        }
}
}