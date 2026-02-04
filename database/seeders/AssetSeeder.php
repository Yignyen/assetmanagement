<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        // Explicit department (seeder-safe)
        $department = Department::where('name', 'TCRC')->firstOrFail();

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
                ->firstOrFail();

            for ($i = 1; $i <= $qty; $i++) {
                Asset::create([
                    'name'          => $name,
                    'serial_no'     => strtoupper(Str::random(10)),
                    'asset_tag'     => 'AST-' . strtoupper(Str::random(6)),
                    'status'        => 'available',
                    'category_id'   => $category->id,
                    'department_id' => $department->id, // ✅ REQUIRED
                ]);
            }
        }

        // Get Education department
        $education = Department::where('name', 'Education Department')->firstOrFail();

        // Example category (must exist)
        $category = Category::where('name', 'Laptop')
            ->where('type', 'asset')
            ->firstOrFail();

        // Create Education assets
        Asset::create([
            'name'          => 'Education Laptop',
            'serial_no'     => strtoupper(Str::random(10)),
            'asset_tag'     => 'EDU-' . strtoupper(Str::random(6)),
            'status'        => 'available',
            'category_id'   => $category->id,
            'department_id' => $education->id,
        ]);

        Asset::create([
            'name'          => 'Education Projector',
            'serial_no'     => strtoupper(Str::random(10)),
            'asset_tag'     => 'EDU-' . strtoupper(Str::random(6)),
            'status'        => 'available',
            'category_id'   => Category::where('name', 'Display')
                                    ->where('type', 'asset')
                                    ->value('id'),
            'department_id' => $education->id,
        ]);
    }
    }


