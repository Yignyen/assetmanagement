<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Component;
use App\Models\Category;


class ComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $items = [
            ['HDD', '1TB HDD', 1],
            ['HDD', 'External HDD 500GB', 2],
            ['HDD', 'External HDD 1TB', 1],
            ['HDD', 'WD 4TB HDD', 1],
            ['HDD', 'Greenbook Backup HDD', 1],
            ['HDD', 'Portable WD HDD', 1],
            ['SSD', 'Crucial 500GB SSD', 2],
        ];

        foreach ($items as [$categoryName, $name, $qty]) {
            $category = Category::where('name', $categoryName)
                ->where('type', 'component')
                ->first();

            Component::create([
                'name' => $name,
                'total_qty' => $qty,
                'available_qty' => $qty,
                'category_id' => $category->id,
            ]);
        }
    }
}
