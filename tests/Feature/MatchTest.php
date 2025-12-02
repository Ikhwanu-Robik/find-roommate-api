<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Util\Match\MatchUtil;
use Tests\Util\Auth\Login\LoginUtil;

class MatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_matching_profiles_require_authentication(): void
    {
        $response = $this->getJson('/api/match/profiles');

        $response->assertStatus(401);
    }

    public function test_get_matching_profiles_require_gender(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataWithout(['gender']);

        $response = $this->getJson('/api/match/profiles' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('gender');
    }

    public function test_get_matching_profiles_require_binary_gender(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataInvalidate(['gender']);

        $response = $this->getJson('/api/match/profiles' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('gender');
        $response->assertJsonValidationErrors([
            'gender' => 'The gender must be either male or female'
        ]);
    }

    public function test_get_matching_profiles_require_age(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataWithout(['age']);

        $response = $this->getJson('/api/match/profiles' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('age');
    }

    public function test_get_matching_profiles_require_integer_age(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataWithout(['age']);
        // the invalidate functions returns a negative integer age
        // so we add invalid string manually
        $data .= '&age=some-kind-of-string';

        $response = $this->getJson('/api/match/profiles' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('age');
        $response->assertJsonValidationErrors([
            'age' => 'The age field must be an integer'
        ]);
    }

    public function test_get_matching_profiles_require_age_to_be_greater_than_16(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataInvalidate(['age']);

        $response = $this->getJson('/api/match/profiles' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('age');
        $response->assertJsonValidationErrors([
            'age' => 'The age field must be at least 17'
        ]);
    }

    public function test_get_matching_profiles_require_address(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataWithout(['address']);

        $response = $this->getJson('/api/match/profiles' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('address');
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
