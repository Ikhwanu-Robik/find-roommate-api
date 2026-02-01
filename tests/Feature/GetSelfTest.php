<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\CustomerProfile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetSelfTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake();
    }

    public function test_authenticated_user_can_get_his_profile_data(): void
    {
        $user = User::factory()->create();
        $profile = CustomerProfile::factory()->create();
        $profile->user()->save($user);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/me');

        $response->assertOk();
        $response->assertJsonPathCanonicalizing('user', $user->toArray());
    }
}
