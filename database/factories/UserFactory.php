<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake('ID')->regexify('/^08[1-9]{1}\d{1}-{1}\d{4}-\d{2,5}$/'),
            'password' => static::$password ??= Hash::make('password'),
        ];
    }
}
