<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Util\Auth\LoginCredentials;

class SPAStatefulLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_SPA_can_login_statefully(): void
    {
        $data = (new LoginCredentials)->toArray();

        $response = $this->postJson('/login', $data);
        $response->assertOk();

        $this->getJson('/api/me')->assertOk();
    }

    public function test_SPA_cannot_login_statefully_with_incorrect_credentials(): void
    {
        $data = new LoginCredentials();
        $otherData = new LoginCredentials();
        $data = $data->replace(['phone' => $otherData->getPhone()])->toArray();

        $response = $this->postJson('/login', $data);

        $response->assertUnauthorized();
    }

    public function test_SPA_stateful_login_require_phone(): void
    {
        $data = (new LoginCredentials)->exclude(['phone'])->toArray();

        $response = $this->postJson('/api/login', $data);

        $response->assertJsonValidationErrors('phone');        
    }

    public function test_SPA_stateful_login_require_valid_format_phone(): void
    {
        $invalidFormatPhone = fake()->regexify('/^\+62-08[1-9]{1}\d{1}-{1}\d{4}-\d{2,5}$/');
        $data = (new LoginCredentials)->replace(['phone' => $invalidFormatPhone])->toArray();

        $response = $this->postJson('/api/login', $data);

        $response->assertJsonValidationErrors([
            'phone' => 'The phone is not an Indonesian phone number of the required format'
        ]);
    }

    public function test_SPA_stateful_login_require_password(): void
    {
        $data = (new LoginCredentials)->exclude(['password'])->toArray();

        $response = $this->postJson('/api/login', $data);

        $response->assertJsonValidationErrorFor('password');
    }
}
