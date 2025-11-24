<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Tests\Util\Util;
use Tests\Util\Auth\LoginUtil;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;
    private $user;

    public function test_logout_require_active_bearer_token(): void
    {
        Util::setupDatabase();

        $response = $this->postJson('/api/logout');

        $response->assertUnauthorized();
    }

    public function test_user_can_logout(): void
    {
        Util::setupDatabase();
        $loginCredentials = LoginUtil::getLoginData();
        $loginResponse = $this->postJson('/api/login', $loginCredentials);
        $bearerToken = 'Bearer ' . $loginResponse->json('token');
        $headers = [
            'Authorization' => $bearerToken
        ];
        $data = [];

        $logoutResponse = $this->postJson('/api/logout', $data, $headers);

        $logoutResponse->assertOk();
    }
}
