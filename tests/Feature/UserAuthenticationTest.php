<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class UserAuthenticationTest extends TestCase
{
    public function test_user_can_login_with_correct_credentials(): void
    {
        $this->setupDatabase();
        $phone = '081265789189';
        $password = '12345678';
        User::factory()->create(['phone' => $phone, 'password' => $password]);

        $response = $this->postJson('/api/login', [
            'phone' => $phone,
            'password' => $password
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'token'
        ]);
    }

    private function setupDatabase()
    {
        Artisan::call('migrate');
    }
}
