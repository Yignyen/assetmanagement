<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetModel;
use App\Models\Category;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Department → Assets mapping
        |--------------------------------------------------------------------------
        | Models are GLOBAL
        | Assets are per-department
        */
        $data = [
            'TCRC' => [
                ['Laptop', 'Lenovo X240 Laptop', 2],
                ['Laptop', 'MacBook Pro 14"', 8],
                ['Desktop', 'Dell Optiplex All-in-One 9010', 12],
                ['Display', 'Dell Monitor 20"', 7],
                ['Furniture', 'Chairs', 17],
            ],
            'Education Department' => [
                ['Laptop', 'MacBook Pro 14"', 3],
                ['Display', 'Dell Monitor 20"', 5],
                ['Furniture', 'Chairs', 10],
            ],
        ];

        foreach ($data as $departmentName => $items) {

            $department = Department::where('name', $departmentName)->firstOrFail();

            foreach ($items as [$categoryName, $modelName, $qty]) {

                // Category
                $category = Category::where('name', $categoryName)
                    ->where('type', 'asset')
                    ->firstOrFail();

                // Model (GLOBAL, created once)
                $model = AssetModel::firstOrCreate([
                    'name' => $modelName,
                    'category_id' => $category->id,
                ]);

                // Assets (per department)
                for ($i = 1; $i <= $qty; $i++) {
                    Asset::create([
                        'name'          => null, // display name
                        'model_id'      => $model->id,
                        'serial_no'     => strtoupper(Str::random(10)),
                        'asset_tag'     => strtoupper($departmentName[0]) . '-' . strtoupper(Str::random(6)),
                        'status'        => 'available',
                        'department_id' => $department->id,
                    ]);
                }
            }
        }
    }
}
