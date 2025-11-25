<?php

namespace Tests\Util\Auth;

class SignupAttributes
{
    private $signupAttributes;
    private $invalidSignupAttributes;

    public function __construct()
    {
        $this->signupAttributes = $this->createSignupAttributes();
        $this->invalidSignupAttributes = $this->createInvalidAttributes();
    }

    private function createSignupAttributes()
    {
        return [
            'name' => fake()->name(),
            'phone' => '0812-2938-2333', // faker doesn't support localization here
            'password' => fake()->password(),
            'birthdate' => fake()->date(),
            'gender' => fake()->randomElement(['male', 'female']),
            'address' => fake()->address(),
            'bio' => fake()->realText(),
        ];
    }

    private function createInvalidAttributes()
    {
        return [
            'name' => null,
            'phone' => '+628122908228',
            'password' => null,
            'birthdate' => now()->toDateString(),
            'gender' => 'transgender'
        ];
    }

    public function exclude($exclusions)
    {
        $filteredSignupAttributes = array_diff_key(
            $this->signupAttributes,
            array_flip($exclusions)
        );

        return $filteredSignupAttributes;
    }

    public function invalidate($keysToInvalidate)
    {
        $filteredInvalidAttributes = $this->filterInvalidAttributes($keysToInvalidate);
        $attributesWithInvalids = $this->replaceWithInvalid($filteredInvalidAttributes);

        return $attributesWithInvalids;
    }

    private function filterInvalidAttributes($keysToInvalidate)
    {
        $filteredInvalidSignupAttributes = [];
        foreach ($keysToInvalidate as $key) {
            $filteredInvalidSignupAttributes[$key] = $this->invalidSignupAttributes[$key];
        }
        return $filteredInvalidSignupAttributes;
    }

    private function replaceWithInvalid($invalids)
    {
        foreach ($invalids as $key => $value) {
            $this->signupAttributes[$key] = $value;
        }
        return $this->signupAttributes;
    }
}
