<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'gender' => fake()->randomElement(['male', 'female']),
            'birthdate' => fake()->date(),
            'address' => fake()->address(),
            'bio' => fake()->realText()
        ];
    }
}
