<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class UserAuthenticationTest extends TestCase
{
    private $user;

    public function test_user_can_login_with_correct_credentials(): void
    {
        $this->setupDatabase();
        $this->createUser();

        $response = $this->postJson('/api/login', [
            'phone' => $this->user->phone,
            'password' => $this->user->passwordPlain
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'token'
        ]);
    }

    public function test_user_cannot_login_with_incorrect_credentials(): void
    {
        $this->setupDatabase();
        $this->createUser();
        $otherPhone = '0813-3535-6767';

        $response = $this->postJson('/api/login', [
            'phone' => $otherPhone,
            'password' => $this->user->passwordPlain
        ]);

        $response->assertUnauthorized();
    }

    public function test_login_require_phone(): void
    {
        $this->setupDatabase();
        $this->createUser();

        $response = $this->postJson('/api/login', [
            'password' => $this->user->passwordPlain
        ]);

        $response->assertJsonValidationErrors('phone');
    }

    public function test_login_require_valid_format_phone(): void
    {
        $this->setupDatabase();
        $invalidPhone = '0812 2890 0011';

        $response = $this->postJson('/api/login', [
            'phone' => $invalidPhone,
            'password' => fake()->password()
        ]);

        $response->assertJsonValidationErrors([
            'phone' => 'The phone is not an Indonesian phone number of the required format'
        ]);
    }

    public function test_login_require_password(): void
    {
        $this->setupDatabase();
        $this->createUser();

        $response = $this->postJson('/api/login', [
            'phone' => $this->user->phone
        ]);

        $response->assertJsonValidationErrorFor('password');
    }

    public function test_logout_require_active_bearer_token(): void
    {
        $this->setupDatabase();

        $response = $this->postJson('/api/logout');

        $response->assertUnauthorized();
    }

    private function setupDatabase()
    {
        Artisan::call('migrate');
    }

    private function createUser()
    {
        $phone = '0812-6578-9189';
        $password = '12345678';
        $this->user = User::factory()->create(['phone' => $phone, 'password' => $password]);
        $this->user->passwordPlain = $password;
    }
}
