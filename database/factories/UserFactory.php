<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            // FK (will be injected via seeder using recycle)
            'department_id' => null,

            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),

            'email_verified_at' => now(),
            'password' => Hash::make('password'), // default password

            'role' => 'user', // default role
            'location_id' => null,

            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Admin user state
     */
    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => 'admin',
        ]);
    }
}
