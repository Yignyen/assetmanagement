<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        $labels = [
            'CEO Laptop',
            'Admin Office PC',
            'Finance Department System',
            'HR Office Computer',
            'Student Lab Computer',
            'Training Room PC',
            'Reception Desk Computer',
            'Server Room Asset',
        ];

        return [
            'department_id' => 1, // must exist
            'model_id' => AssetModel::inRandomOrder()->value('id'),
            'asset_tag' => 'TCRC-MLD-' . str_pad(
                $this->faker->unique()->numberBetween(1, 999),
                3,
                '0',
                STR_PAD_LEFT
            ),
            'serial_no' => strtoupper($this->faker->unique()->bothify('SN###??')),
            'status' => 'available',
            'purchase_date' => $this->faker->optional()->date(),
            'label' => $this->faker->optional()->randomElement($labels),
        ];
        // ❌ name NOT set (auto-generated in model)
    }
}
