<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::where('name', 'TCRC')->firstOrFail();

        // Admin user
        User::create([
            'name'          => 'Admin User',
            'email'         => 'admin@test.com',
            'password'      => Hash::make('password'),
            'role'          => 'admin',
            'department_id' => $department->id,
        ]);

        // Staff user
        User::create([
            'name'          => 'Staff User',
            'email'         => 'staff@test.com',
            'password'      => Hash::make('password'),
            'role'          => 'staff',
            'department_id' => $department->id,
        ]);

        // HR Manager
        User::create([
            'name'          => 'HR Manager',
            'email'         => 'hr@test.com',
            'password'      => Hash::make('password'),
            'role'          => 'intern',
            'department_id' => $department->id,
        ]);
    }
}
