<?php

namespace Database\Seeders;

use App\Models\Category;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::insert([
        ['name' => 'Laptop',   'type' => 'asset',     'description' => 'Company laptops'],
        ['name' => 'Mouse',    'type' => 'accessory', 'description' => 'External mouse'],
        ['name' => 'RAM',      'type' => 'component', 'description' => 'Memory modules'],
    ]);
    }
}
