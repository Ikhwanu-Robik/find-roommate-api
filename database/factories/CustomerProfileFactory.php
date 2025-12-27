<?php

namespace Database\Factories;

use App\Models\CustomerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerProfileFactory extends Factory
{
    public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female']);
        return [
            'full_name' => fake()->name($gender),
            'gender' => $gender,
            'birthdate' => fake()->date(),
            'address' => fake()->address(),
            'bio' => fake()->realText()
        ];
    }

    public function tagged()
    {
        return $this->state(function (array $attributes) {
            return [];
        })->afterCreating(function (CustomerProfile $customerProfile) {
            $tagsGenerator = app()->make('App\Services\TextTagsGenerator');
            $tags = $tagsGenerator->generate($customerProfile->bio);
            $customerProfile->attachTags($tags);
        });
    }
}
