<?php

namespace Tests\Feature;

use App\Models\Lodging;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\CustomerProfile;
use App\Models\ProfilesListing;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JoinListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_join_profile_listing_require_authentication(): void
    {
        $response = $this->postJson('/api/listing');

        $response->assertUnauthorized();
    }

    public function test_join_profile_listing_require_lodging_id(): void
    {
        $profile = CustomerProfile::factory()->create();
        $user = User::factory()->create();
        $profile->user()->save($user);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/listing');

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors(['lodging_id']);
    }

    public function test_user_can_join_profile_listing(): void
    {
        $profile = CustomerProfile::factory()->create();
        $user = User::factory()->create();
        $profile->user()->save($user);
        Sanctum::actingAs($user);

        $lodging = Lodging::factory()->create();

        $response = $this->postJson('/api/listing', ['lodging_id' => $lodging->id]);

        $response->assertOk();
        $profileInListing = $response->json('profile_in_listing');
        // unsetting these attributes because they have are casted
        unset($profileInListing['created_at'], $profileInListing['updated_at']);
        // also unset this because assertDatabaseHas doesn't account for relation
        unset($profileInListing['customer_profile']);
        $this->assertDatabaseHas(ProfilesListing::class, $profileInListing);
    }

    public function test_join_profile_listing_twice_with_different_lodging_id_changes_it(): void
    {
        $profile = CustomerProfile::factory()->create();
        $user = User::factory()->create();
        $profile->user()->save($user);
        Sanctum::actingAs($user);

        $lodging1 = Lodging::factory()->create();
        $response1 = $this->postJson('/api/listing', ['lodging_id' => $lodging1->id]);
        $profileInListing1 = $response1->json('profile_in_listing');

        $lodging2 = Lodging::factory()->create();
        $response2 = $this->postJson('/api/listing', ['lodging_id' => $lodging2->id]);
        $profileInListing2 = $response2->json('profile_in_listing');

        $this->assertNotSame($profileInListing1['lodging_id'], $profileInListing2['lodging_id']);
    }
}
