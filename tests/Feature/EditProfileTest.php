<?php

namespace Tests\Feature;

use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\CustomerProfile;
use Tests\Util\Profiles\ProfileAttribute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cant_edit_other_users_profile(): void
    {
        $user = User::factory()->create();
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);
        Sanctum::actingAs($user);

        $otherUser = User::factory()->create();
        $otherProfile = CustomerProfile::factory()->create();
        $otherProfile->user()->save($otherUser);

        $data = (new ProfileAttribute)->toArray();

        $response = $this->putJson('/api/profiles/' . $otherProfile->id, $data);

        $response->assertForbidden();
        $response->assertExactJson([
            'message' => 'You can only edit your own profile',
        ]);
    }

    public function test_can_edit_profile_full_name(): void
    {
        $user = User::factory()->create();
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);
        Sanctum::actingAs($user);

        $data = (new ProfileAttribute)->only(['full_name'])->toArray();

        $response = $this->putJson('/api/profiles/' . $customerProfile->id, $data);

        $response->assertOk();

        $oldFullName = $customerProfile->full_name;
        $newFullName = CustomerProfile::find($customerProfile->id)->full_name;
        $this->assertNotSame(
            $oldFullName,
            $newFullName
        );
    }

}
