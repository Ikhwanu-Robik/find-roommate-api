<?php

namespace Tests\Util\Auth\Signup;

use Illuminate\Http\UploadedFile;

class InvalidSignupAttributes
{
    private $invalidAttributes;

    public function __construct()
    {
        $this->invalidAttributes = $this->createInvalidAttributes();
    }

    private function createInvalidAttributes()
    {
        return [
            'name' => null,
            'phone' => '+628122908228',
            'password' => null,
            'birthdate' => now()->toDateString(),
            'gender' => 'transgender',
            'profile_photo' => UploadedFile::fake()->create('not-image.json', 20, 'application/json'),
        ];
    }

    public function filterByKeys($keys)
    {
        $filtered = [];
        foreach ($keys as $key) {
            $filtered[$key] = $this->invalidAttributes[$key];
        }
        return $filtered;
    }
}