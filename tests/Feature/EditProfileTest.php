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

    public function test_edit_profile_require_authentication(): void
    {
        $user = User::factory()->create();
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);
        
        $data = (new ProfileAttribute)->toArray();

        $response = $this->putJson('/api/profiles/' . $customerProfile->id, $data);

        $response->assertUnauthorized();
    }

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

    public function test_can_edit_profile_birthdate(): void
    {
        $user = User::factory()->create();
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);
        Sanctum::actingAs($user);

        $data = (new ProfileAttribute)->only(['birthdate'])->toArray();

        $response = $this->putJson('/api/profiles/' . $customerProfile->id, $data);

        $response->assertOk();

        $oldBirthdate = $customerProfile->birthdate;
        $newBirthdate = CustomerProfile::find($customerProfile->id)->birthdate;
        $this->assertNotSame(
            $oldBirthdate,
            $newBirthdate
        );
    }

    public function test_edit_profile_birthdate_must_be_past_date(): void
    {
        $user = User::factory()->create();
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);
        Sanctum::actingAs($user);

        $todayDate = now()->toDateString();
        $data = (new ProfileAttribute)->replace(['birthdate' => $todayDate])->toArray();

        $response = $this->putJson('/api/profiles/' . $customerProfile->id, $data);

        $response->assertOnlyJsonValidationErrors(['birthdate']);
    }

    public function test_can_edit_profile_gender(): void
    {
        $user = User::factory()->create();
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);
        Sanctum::actingAs($user);

        $replacingGender = $customerProfile->gender == 'male' ? 'female' : 'male';
        $data = (new ProfileAttribute)->replace(['gender' => $replacingGender])->toArray();

        $response = $this->putJson('/api/profiles/' . $customerProfile->id, $data);

        $response->assertOk();

        $oldGender = $customerProfile->gender;
        $newGender = CustomerProfile::find($customerProfile->id)->gender;
        $this->assertNotSame(
            $oldGender,
            $newGender
        );
    }

    public function test_edit_profile_gender_must_be_binary(): void
    {
        $user = User::factory()->create();
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);
        Sanctum::actingAs($user);

        $data = (new ProfileAttribute)->replace(['gender' => 'non-binary'])->toArray();

        $response = $this->putJson('/api/profiles/' . $customerProfile->id, $data);

        $response->assertOnlyJsonValidationErrors(['gender']);
    }

    public function test_can_edit_profile_address(): void
    {
        $user = User::factory()->create();
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);
        Sanctum::actingAs($user);

        $data = (new ProfileAttribute)->only(['address'])->toArray();

        $response = $this->putJson('/api/profiles/' . $customerProfile->id, $data);

        $response->assertOk();

        $oldAddress = $customerProfile->address;
        $newAddress = CustomerProfile::find($customerProfile->id)->address;
        $this->assertNotSame(
            $oldAddress,
            $newAddress
        );
    }
}
