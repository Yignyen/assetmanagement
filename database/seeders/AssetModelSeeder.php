<?php

namespace Database\Seeders;

use App\Models\AssetModel;
use App\Models\Category;
use Illuminate\Database\Seeder;

class AssetModelSeeder extends Seeder
{
    public function run(): void
    {
        $models = [
            ['Laptop', 'Lenovo X240 Laptop'],
            ['Laptop', 'MacBook Pro 14"'],
            ['Desktop', 'Dell Optiplex All-in-One 9010'],
            ['Display', 'Dell Monitor 20"'],
            ['Furniture', 'Chair'],
        ];

        foreach ($models as [$categoryName, $modelName]) {
            $category = Category::where('name', $categoryName)
                ->where('type', 'asset')
                ->firstOrFail();

            AssetModel::firstOrCreate([
                'name' => $modelName,
                'category_id' => $category->id,
            ]);
        }
    }
}
