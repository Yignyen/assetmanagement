<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::all();

        if ($departments->isEmpty()) {
            $this->command->error('No departments found. Run DepartmentSeeder first.');
            return;
        }

        $departmentId = $departments->first()->id;

        // 1️⃣ Admin user
        User::factory()
            ->admin()
            ->create([
                'department_id' => $departmentId,
                'name' => 'Admin User',
                'email' => 'admin@example.com',
            ]);

        // 2️⃣ Normal users
        User::factory()
            ->count(5)
            ->create([
                'department_id' => $departmentId,
            ]);
    }
}
