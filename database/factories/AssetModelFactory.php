<?php

namespace Database\Factories;

use App\Models\AssetModel;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetModelFactory extends Factory
{
    protected $model = AssetModel::class;

    public function definition(): array
    {
        $modelsByCategory = [
            'Laptops' => [
                'Dell Latitude 5420',
                'HP ProBook 440 G8',
                'Lenovo ThinkPad E14',
            ],
            'Desktop Computers' => [
                'Dell OptiPlex 7090',
                'HP EliteDesk 800',
            ],
            'Monitors' => [
                'Dell 24-inch Monitor',
                'HP 22-inch Monitor',
                'LG 27-inch Monitor',
            ],
            'Printers' => [
                'HP LaserJet Pro',
                'Canon Office Printer',
            ],
            'Networking Equipment' => [
                '24-Port Network Switch',
                'Wireless Router',
            ],
            'Servers' => [
                'Dell PowerEdge Server',
                'HP ProLiant Server',
            ],
            'UPS & Power Backup' => [
                'APC UPS 1kVA',
                'Online UPS 2kVA',
            ],
            'Computer Lab Systems' => [
                'Student Lab Computer',
                'Training Room PC',
            ],
        ];

        $category = Category::inRandomOrder()->first();

        return [
            'name' => $this->faker->randomElement(
                $modelsByCategory[$category->name] ?? ['Generic Asset Model']
            ),
            'category_id' => $category->id,
            'require_serial' => true,
        ];
    }
}
