<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Tests\Util\Util;
use Tests\Util\Auth\LoginUtil;

class LoginTest extends TestCase
{
    public function test_user_can_login_with_correct_credentials(): void
    {
        Util::setupDatabase();
        $data = LoginUtil::getLoginData();

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
        $data = LoginUtil::getLoginDataWithout(['phone']);

        $response = $this->postJson('/api/login', $data);

        $response->assertJsonValidationErrors('phone');
    }

    public function test_login_require_valid_format_phone(): void
    {
        Util::setupDatabase();
        $data = LoginUtil::getLoginDataInvalidate(['phone']);

        $response = $this->postJson('/api/login', $data);

        $response->assertJsonValidationErrors([
            'phone' => 'The phone is not an Indonesian phone number of the required format'
        ]);
    }

    public function test_login_require_password(): void
    {
        Util::setupDatabase();
        $data = LoginUtil::getLoginDataWithout(['password']);

        $response = $this->postJson('/api/login', $data);

        $response->assertJsonValidationErrorFor('password');
    }
}
