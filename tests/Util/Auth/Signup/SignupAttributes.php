<?php

namespace Tests\Util\Auth\Signup;

use Illuminate\Http\UploadedFile;

class SignupAttributes
{
    private $attributes;
    private $invalidAttributes;

    public function __construct()
    {
        $this->attributes = $this->createAttributes();
        $this->invalidAttributes = new InvalidSignupAttributes();
    }

    private function createAttributes()
    {
        return [
            'name' => fake()->name(),
            'phone' => fake('ID')->regexify('/^08[1-9]{1}\d{1}-{1}\d{4}-\d{2,5}$/'),
            'password' => fake()->password(),
            'birthdate' => fake()->date(),
            'gender' => fake()->randomElement(['male', 'female']),
            'address' => fake()->address(),
            'bio' => fake()->realText(),
            'profile_photo' => UploadedFile::fake()->image('profile_photo.jpg'),
        ];
    }

    public function exclude($exclusions)
    {
        $filteredAttributes = array_diff_key(
            $this->attributes,
            array_flip($exclusions)
        );

        return $filteredAttributes;
    }

    public function invalidate($keysToInvalidate)
    {
        $filteredInvalidAttributes = $this->invalidAttributes->filterByKeys($keysToInvalidate);
        $attributesWithInvalids = $this->replaceWithInvalid($filteredInvalidAttributes);

        return $attributesWithInvalids;
    }

    private function replaceWithInvalid($invalids)
    {
        foreach ($invalids as $key => $value) {
            $this->attributes[$key] = $value;
        }
        return $this->attributes;
    }
}
