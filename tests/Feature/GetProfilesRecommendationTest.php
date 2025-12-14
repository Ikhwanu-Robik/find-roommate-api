<?php

namespace Tests\Feature;

use App\Models\CustomerProfile;
use Tests\TestCase;
use App\Models\User;
use App\Models\ProfilesListing;
use Tests\Util\Match\MatchUtil;
use Database\Seeders\LodgingSeeder;
use Tests\Util\Auth\Signup\SignupUtil;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetProfilesRecommendationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(LodgingSeeder::class);
    }

    public function test_get_profiles_recommendation_require_authentication(): void
    {
        $response = $this->getJson('/api/match/profiles-recommendation');

        $response->assertStatus(401);
    }

    public function test_get_profiles_recommendation_require_gender(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataWithout(['gender']);

        $response = $this->getJson('/api/match/profiles-recommendation' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('gender');
    }

    public function test_get_profiles_recommendation_require_binary_gender(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataInvalidate(['gender']);

        $response = $this->getJson('/api/match/profiles-recommendation' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('gender');
        $response->assertJsonValidationErrors([
            'gender' => 'The gender must be either male or female'
        ]);
    }

    public function test_get_profiles_recommendation_require_min_age(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataWithout(['min_age']);

        $response = $this->getJson('/api/match/profiles-recommendation' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('min_age');
    }

    public function test_get_profiles_recommendation_require_min_age_to_be_integer(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataWithout(['min_age']);
        // getQueryInvalidate(['min_age']) can only return a negative integer age
        // so we add invalid "string" age manually
        $data .= '&min_age=some-string';

        $response = $this->getJson('/api/match/profiles-recommendation' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('min_age');
        $response->assertJsonValidationErrors([
            'min_age' => 'The min age field must be an integer'
        ]);
    }

    public function test_get_profiles_recommendation_require_min_age_to_be_at_least_17(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataInvalidate(['min_age']);

        $response = $this->getJson('/api/match/profiles-recommendation' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('min_age');
        $response->assertJsonValidationErrors([
            'min_age' => 'The min age field must be at least 17'
        ]);
    }

    public function test_get_profiles_recommendation_require_max_age(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataWithout(['max_age']);

        $response = $this->getJson('/api/match/profiles-recommendation' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('max_age');
        $response->assertJsonValidationErrors([
            'max_age' => 'The max age field is required'
        ]);
    }

    public function test_get_profiles_recommendation_require_max_age_to_be_integer(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataWithout(['max_age']);
        // getQueryInvalidate(['max_age']) can only return a negative integer age
        // so we add invalid "string" age manually
        $data .= '&max_age=some-string';

        $response = $this->getJson('/api/match/profiles-recommendation' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('max_age');
        $response->assertJsonValidationErrors([
            'max_age' => 'The max age field must be an integer'
        ]);
    }

    public function test_get_profiles_recommendation_require_max_age_to_be_greater_than_or_equal_to_min_age(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataInvalidate(['max_age']);
        $minAge = MatchUtil::extractQueryValue($data, 'min_age');

        $response = $this->getJson('/api/match/profiles-recommendation' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('max_age');
        $response->assertJsonValidationErrors([
            'max_age' => 'The max age field must be greater than or equal to ' . $minAge,
        ]);
    }

    public function test_get_profiles_recommendation_require_lodging_id(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataWithout(['lodging_id']);

        $response = $this->getJson('/api/match/profiles-recommendation' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('lodging_id');
    }

    public function test_get_profiles_recommendation_require_lodging_id_to_correspond_to_existing_lodging(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataInvalidate(['lodging_id']);

        $response = $this->getJson('/api/match/profiles-recommendation' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('lodging_id');
        $response->assertJsonValidationErrors([
            'lodging_id' => 'The selected lodging id is invalid'
        ]);
    }

    public function test_get_profiles_recommendation_require_bio(): void
    {
        $headers = $this->createHeaders();
        $data = MatchUtil::getQueryDataWithout(['bio']);

        $response = $this->getJson('/api/match/profiles-recommendation' . $data, $headers);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('bio');
    }

    public function test_profiles_recommendation_match_given_criteria(): void
    {
        $headers = $this->createHeaders();
        [$expectedProfiles, $unexpectedProfiles] = $this->createDifferingProfilesInListing();
        $data = $this->getDefaultQuery();

        $response = $this->getJson('/api/match/profiles-recommendation' . $data, $headers);

        $response->assertOk();
        $response->assertExactJson(['matching_profiles' => $expectedProfiles]);
    }

    // these utility functions are here
    // because they need to ->postJson()
    private function createHeaders()
    {
        $credentials = $this->signupAndGetCredentials();
        $bearerToken = $this->loginAndGetBearerToken($credentials);
        return ['Authorization' => $bearerToken];
    }

    private function signupAndGetCredentials()
    {
        $signupData = SignupUtil::getSignupAttributesWithout([]);
        $this->postJson('/api/signup', $signupData);

        $credentials = [
            'phone' => $signupData['phone'],
            'password' => $signupData['password'],
        ];

        return $credentials;
    }

    private function loginAndGetBearerToken(array $user)
    {
        $loginCredentials = [
            'phone' => $user['phone'],
            'password' => $user['password'],
        ];

        $loginResponse = $this->postJson('/api/login', $loginCredentials);

        $bearerToken = 'Bearer ' . $loginResponse->json('token');
        return $bearerToken;
    }

    private function createDifferingProfilesInListing()
    {
        $attributes = [
            'gender' => 'male',
            'age' => 26,
            'lodging_id' => 1,
            'bio' => 'i use arch btw',
        ];
        $expectedProfile = $this->createProfile($attributes);
        $expectedProfileInListing = $this->putIntoListing(
            $expectedProfile,
            $attributes['lodging_id']
        );

        $unexpectedAttributes = [
            'gender' => 'female',
            'age' => 34,
            'lodging_id' => 2,
            'bio' => 'i use windows 11',
        ];
        $unexpectedProfile = $this->createProfile($attributes);
        $unexpectedProfileInListing = $this->putIntoListing(
            $unexpectedProfile,
            $unexpectedAttributes['lodging_id']
        );

        return [$expectedProfileInListing, $unexpectedProfileInListing];
    }

    private function createProfile(
        array $attributes = [
            'gender',
            'age',
            'bio',
        ]
    ) {
        $attributes = $this->replaceAgeWithBirthdate($attributes);

        $signupUser = $this->signupCustomizeSomeAttributes($attributes);
        $user = User::find($signupUser['id']);

        return $user->profile;
    }

    private function putIntoListing(CustomerProfile $profile, string $lodgingId)
    {
        ProfilesListing::create([
            'customer_profile_id' => $profile->id,
            'lodging_id' => $lodgingId,
        ]);
        $profileInListing = ProfilesListing::with(['customerProfile', 'lodging'])
            ->where('customer_profile_id', $profile->id)
            ->get()->toArray();
        // using get(), with(), and toArray() so it
        // matches with what the API returns

        return $profileInListing;
    }

    private function replaceAgeWithBirthdate(array $properties)
    {
        $birthdate = MatchUtil::getBirthdateWhereAge($properties['age']);
        unset($properties['age']);
        $properties['birthdate'] = $birthdate;

        return $properties;
    }

    private function signupCustomizeSomeAttributes(array $attributes)
    {
        $keysToCustomize = array_keys($attributes);
        $signupData = SignupUtil::getSignupAttributesWithout($keysToCustomize);

        foreach ($attributes as $key => $value) {
            $signupData[$key] = $value;
        }

        $response = $this->postJson('/api/signup', $signupData);

        return $response->json('user');
    }

    private function getDefaultQuery()
    {
        $query = MatchUtil::getQueryDataWithout([
            'gender',
            'min_age',
            'max_age',
            'lodging_id',
            'bio'
        ]);
        // the query data are random, so we just exclude
        // and add manually the data we need to control
        $query .= '&gender=male&min_age=17&max_age=26&lodging_id=1&bio=i use arch btw';

        return $query;
    }
}
