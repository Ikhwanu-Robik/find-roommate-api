<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SPAStatefulLogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_stateful_user_can_logout(): void
    {
        $this->actingAs(User::factory()->create());

        $logoutResponse = $this->postJson('/logout');

        $logoutResponse->assertOk();
        $this->getJson('/api/me')->assertUnauthorized();
    }

    public function test_stateful_logout_require_authentication(): void
    {
        $response = $this->postJson('/api/logout');

        $response->assertUnauthorized();
    }
}
