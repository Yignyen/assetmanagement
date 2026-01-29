<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   

        // Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'department' => 'TCRC',
        ]);

        // Normal staff
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@test.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'department' => 'TCRC',
             ]);
        
             // Another sample intern
        User::create([
            'name' => 'HR Manager',
            'email' => 'hr@test.com',
            'password' => Hash::make('password'),
            'role' => 'intern',
            'department' => 'TCRC',
        ]);
    }
}
