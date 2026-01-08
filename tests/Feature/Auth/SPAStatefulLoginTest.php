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
}
