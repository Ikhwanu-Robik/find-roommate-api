<?php

namespace Database\Factories;

use App\Models\CustomerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

class CustomerProfileFactory extends Factory
{
    public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female']);
        $pathToProfilePhoto = UploadedFile::fake()
            ->image('profile_photo.jpg')->store('profile_pics');
        return [
            'full_name' => fake()->name($gender),
            'gender' => $gender,
            'birthdate' => fake()->date(),
            'address' => fake()->address(),
            'bio' => fake()->realText(),
            'profile_photo' => $pathToProfilePhoto
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
