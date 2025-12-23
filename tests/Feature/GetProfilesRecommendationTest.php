<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\CustomerProfile;
use App\Models\ProfilesListing;
use Tests\Util\Match\MatchUtil;
use Database\Seeders\LodgingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Util\Match\MatchAssertions;
use Tests\Util\Match\MatchInputs;

class GetProfilesRecommendationTest extends TestCase
{
    use RefreshDatabase, MatchAssertions;

    private $matchInputs;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(LodgingSeeder::class);

        $this->matchInputs = new MatchInputs();
    }

    public function test_get_profiles_recommendation_require_authentication(): void
    {
        $response = $this->getJson('/api/match/profiles-recommendation');

        $response->assertStatus(401);
    }

    public function test_get_profiles_recommendation_require_gender(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $data = $this->matchInputs->exclude(['gender']);

        $response = $this->getJson('/api/match/profiles-recommendation' . '?' . http_build_query($data));

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('gender');
    }

    public function test_get_profiles_recommendation_require_binary_gender(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $data = $this->matchInputs->replace(['gender' => 'non-binary']);

        $response = $this->getJson('/api/match/profiles-recommendation' . '?' . http_build_query($data));

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('gender');
        $response->assertJsonValidationErrors([
            'gender' => 'The gender must be either male or female'
        ]);
    }

    public function test_get_profiles_recommendation_require_min_age(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $data = $this->matchInputs->exclude(['min_age']);

        $response = $this->getJson('/api/match/profiles-recommendation' . '?' . http_build_query($data));

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('min_age');
    }

    public function test_get_profiles_recommendation_require_min_age_to_be_integer(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $data = $this->matchInputs->replace(['min_age' => 'some string']);

        $response = $this->getJson('/api/match/profiles-recommendation' . '?' . http_build_query($data));

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('min_age');
        $response->assertJsonValidationErrors([
            'min_age' => 'The min age field must be an integer'
        ]);
    }

    public function test_get_profiles_recommendation_require_min_age_to_be_at_least_17(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $data = $this->matchInputs->replace(['min_age' => 16]);

        $response = $this->getJson('/api/match/profiles-recommendation' . '?' . http_build_query($data));

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('min_age');
        $response->assertJsonValidationErrors([
            'min_age' => 'The min age field must be at least 17'
        ]);
    }

    public function test_get_profiles_recommendation_require_max_age(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $data = $this->matchInputs->exclude(['max_age']);

        $response = $this->getJson('/api/match/profiles-recommendation' . '?' . http_build_query($data));

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('max_age');
        $response->assertJsonValidationErrors([
            'max_age' => 'The max age field is required'
        ]);
    }

    public function test_get_profiles_recommendation_require_max_age_to_be_integer(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $data = $this->matchInputs->replace(['max_age' => 'some string']);

        $response = $this->getJson('/api/match/profiles-recommendation' . '?' . http_build_query($data));

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('max_age');
        $response->assertJsonValidationErrors([
            'max_age' => 'The max age field must be an integer'
        ]);
    }

    public function test_get_profiles_recommendation_require_max_age_to_be_greater_than_or_equal_to_min_age(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $data = $this->matchInputs->replace(['min_age' => 17, 'max_age' => 16]);

        $response = $this->getJson('/api/match/profiles-recommendation' . '?' . http_build_query($data));

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('max_age');
        $response->assertJsonValidationErrors([
            'max_age' => 'The max age field must be greater than or equal to ' . $data['min_age'],
        ]);
    }

    public function test_get_profiles_recommendation_require_lodging_id(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $data = $this->matchInputs->exclude(['lodging_id']);

        $response = $this->getJson('/api/match/profiles-recommendation' . '?' . http_build_query($data));

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('lodging_id');
    }

    public function test_get_profiles_recommendation_require_lodging_id_to_correspond_to_existing_lodging(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $data = $this->matchInputs->replace(['lodging_id' => -1]);

        $response = $this->getJson('/api/match/profiles-recommendation' . '?' . http_build_query($data));

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('lodging_id');
        $response->assertJsonValidationErrors([
            'lodging_id' => 'The selected lodging id is invalid'
        ]);
    }

    public function test_get_profiles_recommendation_require_bio(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $data = $this->matchInputs->exclude(['bio']);

        $response = $this->getJson('/api/match/profiles-recommendation' . '?' . http_build_query($data));

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('bio');
    }

    public function test_profiles_recommendation_match_given_criteria(): void
    {
        // create the user making the request
        $customerProfile = CustomerProfile::factory()->create();
        $user = User::factory()->create();
        $customerProfile->user()->save($user);
        Sanctum::actingAs($user);

        // create the profiles that should be returned by the API
        $criteria = ['min_age' => 17, 'max_age' => 27, 'gender' => 'male', 'lodging_id' => 1, 'bio' => 'i use arch btw'];
        $expectedAttributes = MatchUtil::createAttributesFromCriteria($criteria);
        $expectedProfiles = CustomerProfile::factory()->tagged()->create($expectedAttributes);

        // create the profiles that should NOT be returned by the API
        $nonCriteria = ['min_age' => 28, 'max_age' => 34, 'gender' => 'female', 'lodging_id' => 2, 'bio' => 'i use windows 11'];
        $unexpectedAttributes = MatchUtil::createAttributesFromCriteria($nonCriteria);
        $unexpectedProfiles = CustomerProfile::factory()->tagged()->create($unexpectedAttributes);

        // putting the profiles in match's profiles listing
        ProfilesListing::create([
            'customer_profile_id' => $expectedProfiles->id,
            'lodging_id' => $criteria['lodging_id']
        ]);
        ProfilesListing::create([
            'customer_profile_id' => $unexpectedProfiles->id,
            'lodging_id' => $nonCriteria['lodging_id']
        ]);

        $response = $this->getJson('/api/match/profiles-recommendation' . '?' . http_build_query($criteria));

        $response->assertOk();
        $response->assertJsonCount(1, 'matching_profiles');
        
        $profileInListing = $response->json('matching_profiles')[0];
        $profile = $profileInListing['customer_profile'];

        $this->assertSame($criteria['gender'], $profile['gender']);
        $this->assertSame($criteria['lodging_id'], $profileInListing['lodging_id']);

        $this->assertSimilarBio($criteria['bio'], $profile['bio']);

        $age = MatchUtil::birthdateToAge($profile['birthdate']);
        $this->assertGreaterThanOrEqual($criteria['min_age'], $age);
        $this->assertLessThanOrEqual($criteria['max_age'], $age);
    }
}
