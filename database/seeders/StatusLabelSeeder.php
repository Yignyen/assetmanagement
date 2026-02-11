<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StatusLabel;

class StatusLabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StatusLabel::insert([
    [
        'name' => 'Ready to Deploy',
        'deployable' => true,
        'pending' => false,
        'archived' => false,
        'default_label' => true,
        'color' => 'green',
    ],
    [
        'name' => 'Pending',
        'deployable' => false,
        'pending' => true,
        'archived' => false,
        'default_label' => false,
        'color' => 'blue',
    ],
    [
        'name' => 'Out for Diagnostics',
        'deployable' => false,
        'pending' => false,
        'archived' => false,
        'default_label' => false,
        'color' => 'orange',
    ],
    [
        'name' => 'Out for Repair',
        'deployable' => false,
        'pending' => false,
        'archived' => false,
        'default_label' => false,
        'color' => 'orange',
    ],
    [
        'name' => 'Broken - Not Fixable',
        'deployable' => false,
        'pending' => false,
        'archived' => false,
        'default_label' => false,
        'color' => 'red',
    ],
    [
        'name' => 'Lost/Stolen',
        'deployable' => false,
        'pending' => false,
        'archived' => false,
        'default_label' => false,
        'color' => 'darkred',
    ],
    [
        'name' => 'Archived',
        'deployable' => false,
        'pending' => false,
        'archived' => true,
        'default_label' => false,
        'color' => 'gray',
    ],
]);

    }
}
