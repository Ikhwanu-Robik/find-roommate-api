<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Util\Util;
use Tests\Util\Auth\LoginUtil;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_correct_credentials(): void
    {
        Util::setupDatabase();
        $data = LoginUtil::getLoginCredentialsWithout([]);

        $response = $this->postJson('/api/login', $data);

        $response->assertOk();
        $response->assertJsonStructure([
            'token'
        ]);
    }

    public function test_user_cannot_login_with_incorrect_credentials(): void
    {
        Util::setupDatabase();
        $data = LoginUtil::getIncorrectLoginData();

        $response = $this->postJson('/api/login', $data);

        $response->assertUnauthorized();
    }

    public function test_login_require_phone(): void
    {
        Util::setupDatabase();
        $data = LoginUtil::getLoginCredentialsWithout(['phone']);

        $response = $this->postJson('/api/login', $data);

        $response->assertJsonValidationErrors('phone');
    }

    public function test_login_require_valid_format_phone(): void
    {
        Util::setupDatabase();
        $data = LoginUtil::getLoginCredentialsInvalidate(['phone']);

        $response = $this->postJson('/api/login', $data);

        $response->assertJsonValidationErrors([
            'phone' => 'The phone is not an Indonesian phone number of the required format'
        ]);
    }

    public function test_login_require_password(): void
    {
        Util::setupDatabase();
        $data = LoginUtil::getLoginCredentialsWithout(['password']);

        $response = $this->postJson('/api/login', $data);

        $response->assertJsonValidationErrorFor('password');
    }
}
