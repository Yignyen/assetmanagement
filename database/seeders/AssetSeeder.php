<?php

namespace Database\Seeders;

use App\Models\Asset;

use App\Models\Category;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $items = [
            ['Laptop', 'Lenovo X240 Laptop', 2],
            ['Laptop', 'MacBook Pro 14"', 8],
            ['Desktop', 'Dell Optiplex All-in-One 9010', 12],
            ['Display', 'Dell Monitor 20"', 7],
            ['Furniture', 'Chairs', 17],
        ];

        foreach ($items as [$categoryName, $name, $qty]) {
            $category = Category::where('name', $categoryName)
                ->where('type', 'asset')
                ->first();
            if (!$category) {
                throw new \Exception("Missing asset category: {$categoryName}");
            }
            

            for ($i = 1; $i <= $qty; $i++) {
                Asset::create([
                    'name' => $name,
                    'serial_no' => strtoupper(Str::random(10)),
                    'asset_tag' => 'AST-' . strtoupper(Str::random(6)),
                    'status' => 'available',
                    'category_id' => $category->id,
                ]);
            }
        }
    }
}
    

