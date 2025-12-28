<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_logout(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $data = [];

        $logoutResponse = $this->postJson('/api/logout', $data);

        $logoutResponse->assertOk();
    }
    
    public function test_logout_require_authentication(): void
    {
        $response = $this->postJson('/api/logout');

        $response->assertUnauthorized();
    }
}
