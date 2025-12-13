<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Tests\Util\Auth\Login\LoginUtil;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_logout(): void
    {
        $headers = $this->createHeaders();
        $data = [];

        $logoutResponse = $this->postJson('/api/logout', $data, $headers);

        $logoutResponse->assertOk();
    }
    
    public function test_logout_require_authentication(): void
    {
        $response = $this->postJson('/api/logout');

        $response->assertUnauthorized();
    }

    private function createHeaders()
    {
        $bearerToken = $this->loginAndGetBearerToken();
        return ['Authorization' => $bearerToken];
    }

    private function loginAndGetBearerToken()
    {
        $loginCredentials = LoginUtil::getLoginCredentialsWithout([]);
        $loginResponse = $this->postJson('/api/login', $loginCredentials);

        $bearerToken = 'Bearer ' . $loginResponse->json('token');
        return $bearerToken;
    }
}
