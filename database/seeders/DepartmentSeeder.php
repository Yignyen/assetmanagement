<?php

namespace Database\Seeders;
use App\Models\Department;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $departments = [
            [
                'name'      => 'TCRC',
                'tag_color' => '#1e88e5',
                'notes'     => 'Default support department',
            ],
            [
                'name'      => 'Education Department',
                'tag_color' => '#43a047',
                'notes'     => 'Education department',
            ],
            [
                'name'      => 'Finance Department',
                'tag_color' => '#f4511e',
                'notes'     => 'Finance department',
            ],
        ];

        foreach ($departments as $data) {
            Department::firstOrCreate(
                ['name' => $data['name']],
                $data
        );
    }
}
}