<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'name'              => $this->faker->name(),
            'email'             => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => bcrypt('password123'),
            'remember_token'    => Str::random(10),
            'role'              => 'mahasiswa',
            'bio'               => $this->faker->sentence(10),
            'student_id'        => 'NIM' . $this->faker->numerify('######'),
            'phone'             => $this->faker->phoneNumber(),
        ];
    }

    public function admin()
    {
        return $this->state(['role' => 'admin']);
    }

    public function unverified()
    {
        return $this->state(['email_verified_at' => null]);
    }
}
