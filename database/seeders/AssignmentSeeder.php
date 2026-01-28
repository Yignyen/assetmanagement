<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Asset;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $asset = Asset::first();

    Assignment::create([
        'user_id' => 2,
        'item_type' => Asset::class,
        'item_id' => $asset->id,
        'assigned_at' => now(),
        'status' => 'active',
        'assigned_by' => 1,
        'notes' => 'Issued for office work',
    ]);
    }
}
