<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Tests\Util\Auth\Login\LoginUtil;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_correct_credentials(): void
    {
        $data = LoginUtil::getLoginCredentialsWithout([]);

        $response = $this->postJson('/api/login', $data);

        $response->assertOk();
        $response->assertJsonStructure([
            'token'
        ]);
    }

    public function test_user_cannot_login_with_incorrect_credentials(): void
    {
        $data = LoginUtil::getIncorrectLoginData();

        $response = $this->postJson('/api/login', $data);

        $response->assertUnauthorized();
    }

    public function test_login_require_phone(): void
    {
        $data = LoginUtil::getLoginCredentialsWithout(['phone']);

        $response = $this->postJson('/api/login', $data);

        $response->assertJsonValidationErrors('phone');
    }

    public function test_login_require_valid_format_phone(): void
    {
        $data = LoginUtil::getLoginCredentialsInvalidate(['phone']);

        $response = $this->postJson('/api/login', $data);

        $response->assertJsonValidationErrors([
            'phone' => 'The phone is not an Indonesian phone number of the required format'
        ]);
    }

    public function test_login_require_password(): void
    {
        $data = LoginUtil::getLoginCredentialsWithout(['password']);

        $response = $this->postJson('/api/login', $data);

        $response->assertJsonValidationErrorFor('password');
    }
}
