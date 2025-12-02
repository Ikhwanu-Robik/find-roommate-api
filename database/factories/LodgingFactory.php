<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LodgingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->lastName() . '\'s Lodging',
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
        ];
    }
}
