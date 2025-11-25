<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Tests\Util\Util;
use Tests\Util\Auth\SignupUtil;

class SignupTest extends TestCase
{
    public function test_user_can_signup(): void
    {
        Util::setupDatabase();
        $data = SignupUtil::getSignupAttributesWithout([]);

        $response = $this->postJson('/api/signup', $data);

        $response->assertOk();
        $response->assertJsonStructure([
            'user' => [
                'id',
                'name',
                'phone',
                'gender',
                'birthdate',
                'address',
                'bio'
            ]
        ]);
    }

    public function test_signup_require_name(): void
    {
        $data = SignupUtil::getSignupAttributesWithout(['name']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('name');
    }

    public function test_signup_require_phone(): void
    {
        $data = SignupUtil::getSignupAttributesWithout(['phone']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('phone');
    }

    public function test_signup_require_valid_format_phone(): void
    {
        $data = SignupUtil::getSignupAttributesInvalidate(['phone']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('phone');
        $response->assertOnlyJsonValidationErrors([
            'phone' => 'The phone is not an Indonesian phone number of the required format'
        ]);
    }

    public function test_signup_require_password(): void
    {
        $data = SignupUtil::getSignupAttributesWithout(['password']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('password');
    }

    public function test_signup_require_birthdate(): void
    {
        $data = SignupUtil::getSignupAttributesWithout(['birthdate']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('birthdate');
    }

    public function test_signup_require_birthdate_to_be_past_date(): void
    {
        $data = SignupUtil::getSignupAttributesInvalidate(['birthdate']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('birthdate');
        $response->assertJsonValidationErrors([
            'birthdate' => 'The birthdate must be past date'
        ]);
    }

    public function test_signup_require_gender(): void
    {
        $data = SignupUtil::getSignupAttributesWithout(['gender']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('gender');
    }

    public function test_signup_require_non_binary_gender(): void
    {
        $data = SignupUtil::getSignupAttributesInvalidate(['gender']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('gender');
        $response->assertJsonValidationErrors([
            'gender' => 'The gender must be either male or female'
        ]);
    }

    public function test_signup_require_address(): void
    {
        $data = SignupUtil::getSignupAttributesWithout(['address']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('address');
    }

    public function test_signup_require_bio(): void
    {
        $data = SignupUtil::getSignupAttributesWithout(['bio']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('bio');
    }
}
