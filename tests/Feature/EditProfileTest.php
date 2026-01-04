<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\CustomerProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Util\Profiles\ProfileAttribute;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EditProfileTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake();
    }

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
        $data = (new ProfileAttribute)
            ->replace(['birthdate' => $todayDate])
            ->only(['birthdate'])->toArray();

        $response = $this->putJson('/api/profiles/' . $customerProfile->id, $data);

        $response->assertOnlyJsonValidationErrors([
            'birthdate' => 'The birthdate must be past date'
        ]);
    }

    public function test_can_edit_profile_gender(): void
    {
        $user = User::factory()->create();
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);
        Sanctum::actingAs($user);

        $replacingGender = $customerProfile->gender == 'male' ? 'female' : 'male';
        $data = (new ProfileAttribute)
            ->replace(['gender' => $replacingGender])
            ->only(['gender'])->toArray();

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

        $data = (new ProfileAttribute)
            ->replace(['gender' => 'non-binary'])
            ->only(['gender'])->toArray();

        $response = $this->putJson('/api/profiles/' . $customerProfile->id, $data);

        $response->assertOnlyJsonValidationErrors([
            'gender' => 'The gender must be either male or female'
        ]);
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

    public function test_can_edit_profile_bio(): void
    {
        $user = User::factory()->create();
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);
        Sanctum::actingAs($user);

        $data = (new ProfileAttribute)->only(['bio'])->toArray();

        $response = $this->putJson('/api/profiles/' . $customerProfile->id, $data);

        $response->assertOk();

        $oldBio = $customerProfile->bio;
        $newBio = CustomerProfile::find($customerProfile->id)->bio;
        $this->assertNotSame(
            $oldBio,
            $newBio
        );
    }

    public function test_editing_profile_bio_also_update_profile_tags(): void
    {
        $user = User::factory()->create();
        $customerProfile = CustomerProfile::factory()
            ->tagged()->create(['bio' => 'i use arch btw']);
        $customerProfile->user()->save($user);
        Sanctum::actingAs($user);

        $oldTags = $customerProfile->tags;

        // making sure that the replacing bio has different tags
        $replacingBio = 'i use windows 11';
        $data = (new ProfileAttribute)
            ->replace(['bio' => $replacingBio])
            ->only(['bio'])->toArray();

        $response = $this->putJson('/api/profiles/' . $customerProfile->id, $data);

        $response->assertOk();
        $newTags = CustomerProfile::find($customerProfile->id)->tags;

        // json_encode is used because i deem
        // collection comparison potentially misleading
        $this->assertNotSame(
            json_encode($oldTags),
            json_encode($newTags)
        );
    }

    public function test_can_edit_profile_photo(): void
    {
        $user = User::factory()->create();
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);
        Sanctum::actingAs($user);

        $data = (new ProfileAttribute)->only(['profile_photo'])->toArray();

        $response = $this->putJson('/api/profiles/' . $customerProfile->id, $data);

        $response->assertOk();

        $oldProfilePhoto = $customerProfile->profile_photo;
        $newProfilePhoto = CustomerProfile::find($customerProfile->id)->profile_photo;
        $this->assertNotSame(
            $oldProfilePhoto,
            $newProfilePhoto
        );
    }

    public function test_profile_photo_not_updated_without_image_providedd(): void
    {
        // because profile_photo update requires special logic
        // I thought maybe it needed a dedicated test
        $user = User::factory()->create();
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);
        Sanctum::actingAs($user);

        $data = (new ProfileAttribute)->exclude(['profile_photo'])->toArray();

        $response = $this->putJson('/api/profiles/' . $customerProfile->id, $data);

        $response->assertOk();

        $oldProfilePhoto = $customerProfile->profile_photo;
        $newProfilePhoto = CustomerProfile::find($customerProfile->id)->profile_photo;
        $this->assertSame(
            $oldProfilePhoto,
            $newProfilePhoto
        );
    }

    public function test_edit_profile_photo_must_be_image(): void
    {
        $user = User::factory()->create();
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);
        Sanctum::actingAs($user);

        $jsonFile = UploadedFile::fake()
            ->create('not-image.json', 10, 'application/json');
        $data = (new ProfileAttribute)
            ->replace(['profile_photo' => $jsonFile])
            ->only(['profile_photo'])->toArray();

        $response = $this->putJson('/api/profiles/' . $customerProfile->id, $data);

        $response->assertOnlyJsonValidationErrors([
            'profile_photo' => 'The profile photo field must be an image.'
        ]);
    }
}
