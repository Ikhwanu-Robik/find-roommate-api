<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Tests\Util\Auth\LoginCredentials;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_correct_credentials(): void
    {
        $data = (new LoginCredentials)->exclude([]);

        $response = $this->postJson('/api/login', $data);

        $response->assertOk();
        $response->assertJsonStructure([
            'token'
        ]);
    }

    public function test_user_cannot_login_with_incorrect_credentials(): void
    {
        $data = new LoginCredentials();
        $otherData = new LoginCredentials();
        $data = $data->replace(['phone' => $otherData->getPhone()]);

        $response = $this->postJson('/api/login', $data);

        $response->assertUnauthorized();
    }

    public function test_login_require_phone(): void
    {
        $data = (new LoginCredentials)->exclude(['phone']);

        $response = $this->postJson('/api/login', $data);

        $response->assertJsonValidationErrors('phone');
    }

    public function test_login_require_valid_format_phone(): void
    {
        $invalidFormatPhone = fake()->regexify('/^\+62-08[1-9]{1}\d{1}-{1}\d{4}-\d{2,5}$/');
        $data = (new LoginCredentials)->replace(['phone' => $invalidFormatPhone]);

        $response = $this->postJson('/api/login', $data);

        $response->assertJsonValidationErrors([
            'phone' => 'The phone is not an Indonesian phone number of the required format'
        ]);
    }

    public function test_login_require_password(): void
    {
        $data = (new LoginCredentials)->exclude(['password']);

        $response = $this->postJson('/api/login', $data);

        $response->assertJsonValidationErrorFor('password');
    }
}
