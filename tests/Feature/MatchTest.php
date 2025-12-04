<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Util\Match\MatchUtil;
use Database\Seeders\LodgingSeeder;
use Tests\Util\Auth\Login\LoginUtil;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MatchTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(LodgingSeeder::class);
    }

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
        // getQueryInvalidate(['age']) can only return a negative integer age
        // so we add invalid "string" age manually
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

    public function test_get_matching_profiles_require_lodging_id(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataWithout(['lodging_id']);

        $response = $this->getJson('/api/match/profiles' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('lodging_id');
    }

    public function test_get_matching_profiles_require_lodging_id_to_correspond_to_existing_lodging(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataWithout(['lodging_id']);
        // getQueryInvalidate(['lodging_id']) can only return null
        // so we add invalid lodging_id manually
        $data .= '&lodging_id=-1';

        $response = $this->getJson('/api/match/profiles' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('lodging_id');
        $response->assertJsonValidationErrors([
            'lodging_id' => 'The selected lodging id is invalid'
        ]);
    }

    public function test_get_matching_profiles_require_bio(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataWithout(['bio']);

        $response = $this->getJson('/api/match/profiles' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('bio');
    }

    public function test_get_matching_profiles_with_gender(): void
    {
        $headers = $this->createHeaders();
        $maleProfiles = MatchUtil::createProfiles('male', 1);
        $femaleProfiles = MatchUtil::createProfiles('female', 4);
        $data = MatchUtil::getQueryDataWithout(['gender']);
        // the gender is random, so we exclude it
        // and add the gender manually
        $data .= '&gender=' . 'male';

        $response = $this->getJson('/api/match/profiles' . $data, $headers);

        $response->assertOk();
        $response->assertExactJson([ 
            'matching_profiles' => $maleProfiles,
        ]);
    }

    private function createHeaders()
    {
        // can't put it in MatchUtil because
        // loginAndGetBearerToken need to post json
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
