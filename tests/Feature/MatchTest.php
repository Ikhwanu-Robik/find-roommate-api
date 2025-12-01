<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Util\Match\MatchUtil;
use Tests\Util\Auth\Login\LoginUtil;

class MatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_matching_profiles_require_gender(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataWithout(['gender']);

        $response = $this->getJson('/api/match/profiles' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('gender');
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
