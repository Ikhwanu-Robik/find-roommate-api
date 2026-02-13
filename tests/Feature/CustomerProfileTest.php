<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use Laravel\Sanctum\Sanctum;
use App\Models\CustomerProfile;
use Illuminate\Http\UploadedFile;
use App\Services\TextTagsGenerator;
use Illuminate\Support\Facades\Storage;
use Tests\Util\Profiles\ProfileAttribute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;

class CustomerProfileTest extends TestCase
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

    public function test_edited_profile_photo_is_stored(): void
    {
        $user = User::factory()->create();
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);
        Sanctum::actingAs($user);

        $data = (new ProfileAttribute)->only(['profile_photo'])->toArray();

        $response = $this->putJson('/api/profiles/' . $customerProfile->id, $data);

        $response->assertOk();

        $newProfilePhoto = CustomerProfile::find($customerProfile->id)->profile_photo;
        Storage::assertExists($newProfilePhoto);
    }

    public function test_return_500_if_image_storage_fail(): void
    {
        $user = User::factory()->create();
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);
        Sanctum::actingAs($user);

        $this->mock(FilesystemFactory::class, function (MockInterface $mock) {
            $mock->expects('disk')->andReturnSelf();
            $mock->expects('putFileAs')->andReturnFalse();
        });

        $data = (new ProfileAttribute)->only(['profile_photo'])->toArray();

        $response = $this->putJson('/api/profiles/' . $customerProfile->id, $data);

        $response->assertServerError();
        $response->assertExactJson(['message' => 'Image storage failed']);
    }

    public function test_editing_profile_photo_will_delete_old_profile_photo_from_storage(): void
    {
        $user = User::factory()->create();
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);
        Sanctum::actingAs($user);

        $data = (new ProfileAttribute)->only(['profile_photo'])->toArray();

        $response = $this->putJson('/api/profiles/' . $customerProfile->id, $data);

        $response->assertOk();

        $oldProfilePhoto = $customerProfile->profile_photo;
        Storage::assertMissing($oldProfilePhoto);
    }

    public function test_old_image_is_not_deleted_if_bio_processing_failed(): void
    {
        $user = User::factory()->create();
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);
        Sanctum::actingAs($user);

        $data = (new ProfileAttribute)->toArray();

        $this->mock('App\Services\TextTagsGenerator', function (MockInterface $mockInterface) {
            $mockInterface->shouldReceive('generate')->andThrow(
                'Exception',
                'The connection to api.text-tags-generator took to long to respond',
                500
            );
        });

        $response = $this->putJson('/api/profiles/' . $customerProfile->id, $data);

        $response->assertServerError();

        $oldProfilePhoto = $customerProfile->profile_photo;
        Storage::assertExists($oldProfilePhoto);
    }

    public function test_old_bio_tags_is_preserved_if_edit_profile_failed(): void
    {
        $user = User::factory()->create();
        $customerProfile = CustomerProfile::factory()->tagged()->create();
        $customerProfile->user()->save($user);
        Sanctum::actingAs($user);

        $oldTags = $customerProfile->tags;

        $data = (new ProfileAttribute)->toArray();

        Storage::partialMock()->shouldReceive('delete')->andThrow(
            'Exception',
            'Attempt to save models interuppted intentionally',
            500
        );

        $response = $this->putJson('/api/profiles/' . $customerProfile->id, $data);

        $response->assertServerError();

        $customerProfile->refresh();
        $newTags = $customerProfile->tags;
        $this->assertSame($oldTags->count(), $newTags->count());
        $this->assertSame($oldTags->toArray(), $newTags->toArray());
    }

    
    public function test_can_create_profile(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $createProfileAttribute = (new ProfileAttribute)->toArray();

        $response = $this->post(
            "/api/v2/users/" . $user->id . "/profiles",
            $createProfileAttribute
        );

        $response->assertOk();
        $profile = $response->json('customer_profile');
        // unsetting them because assertDatabaseHas
        // only supports specific format of timestamps
        unset($profile['created_at']);
        unset($profile['updated_at']);
        $this->assertDatabaseHas("customer_profiles", $profile);
    }

    public function test_user_must_be_logged_in_to_create_profile(): void
    {
        $user = User::factory()->create();
        $createProfileAttribute = (new ProfileAttribute)->toArray();

        $response = $this->postJson(
            "/api/v2/users/" . $user->id . "/profiles",
            $createProfileAttribute
        );

        $response->assertUnauthorized();
    }

    public function test_given_user_must_be_the_same_as_current_user_to_create_profile(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $createProfileAttribute = (new ProfileAttribute)->toArray();
        $otherUser = User::factory()->create();

        $response = $this->postJson(
            "/api/v2/users/" . $otherUser->id . "/profiles",
            $createProfileAttribute
        );

        $response->assertForbidden();
        $response->assertJson(['message' => 'You can only create profile for your account']);
    }

    public function test_create_profile_uploaded_image_exists_in_storage(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $createProfileAttribute = (new ProfileAttribute)->toArray();

        $response = $this->post(
            "/api/v2/users/" . $user->id . "/profiles",
            $createProfileAttribute
        );

        $response->assertOk();
        $savedFilePath = $response->json('customer_profile.profile_photo');
        Storage::assertExists($savedFilePath);
    }

    public function test_api_return_a_message_if_image_storage_fail(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $this->mock(FilesystemFactory::class, function (MockInterface $mock) {
            $mock->expects('disk')->andReturnSelf();
            $mock->expects('putFileAs')->andReturnFalse();
        });
        $createProfileAttribute = (new ProfileAttribute())->toArray();

        $response = $this->postJson('/api/v2/users/' . $user->id . '/profiles', $createProfileAttribute);

        $response->assertServerError();
        $response->assertJson([
            'message' => 'Failed to store profile photo'
        ]);
    }

    public function test_create_profile_require_full_name(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $createProfileAttribute = (new ProfileAttribute)->exclude(['full_name'])->toArray();

        $response = $this->postJson('/api/v2/users/' . $user->id . '/profiles', $createProfileAttribute);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('full_name');
    }

    public function test_create_profile_require_birthdate(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $createProfileAttribute = (new ProfileAttribute)->exclude(['birthdate'])->toArray();

        $response = $this->postJson('/api/v2/users/' . $user->id . '/profiles', $createProfileAttribute);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('birthdate');
    }

    public function test_create_profile_require_birthdate_to_be_past_date(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $todayDate = now()->toDateString();
        $createProfileAttribute = (new ProfileAttribute)->replace(['birthdate' => $todayDate])->toArray();

        $response = $this->postJson('/api/v2/users/' . $user->id . '/profiles', $createProfileAttribute);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('birthdate');
        $response->assertJsonValidationErrors([
            'birthdate' => 'The birthdate must be past date'
        ]);
    }

    public function test_create_profile_require_gender(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $createProfileAttribute = (new ProfileAttribute)->exclude(['gender'])->toArray();

        $response = $this->postJson('/api/v2/users/' . $user->id . '/profiles', $createProfileAttribute);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('gender');
    }

    public function test_create_profile_require_binary_gender(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $createProfileAttribute = (new ProfileAttribute)->replace(['gender' => 'non-binary'])->toArray();

        $response = $this->postJson('/api/v2/users/' . $user->id . '/profiles', $createProfileAttribute);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('gender');
        $response->assertJsonValidationErrors([
            'gender' => 'The gender must be either male or female'
        ]);
    }

    public function test_create_profile_require_address(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $createProfileAttribute = (new ProfileAttribute)->exclude(['address'])->toArray();

        $response = $this->postJson('/api/v2/users/' . $user->id . '/profiles', $createProfileAttribute);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('address');
    }

    public function test_create_profile_require_bio(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $createProfileAttribute = (new ProfileAttribute)->exclude(['bio'])->toArray();

        $response = $this->postJson('/api/v2/users/' . $user->id . '/profiles', $createProfileAttribute);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('bio');
    }

    public function test_create_profile_require_profile_photo(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $createProfileAttribute = (new ProfileAttribute)->exclude(['profile_photo'])->toArray();

        $response = $this->postJson('/api/v2/users/' . $user->id . '/profiles', $createProfileAttribute);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('profile_photo');
    }

    public function test_create_profile_require_profile_photo_to_be_image(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $jsonFile = UploadedFile::fake()->create(
            'not-image.json',
            20,
            'application/json'
        );
        $createProfileAttribute = (new ProfileAttribute)->replace(['profile_photo' => $jsonFile])->toArray();

        $response = $this->postJson('/api/v2/users/' . $user->id . '/profiles', $createProfileAttribute);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('profile_photo');
        $response->assertJsonValidationErrors([
            'profile_photo' => 'The profile photo field must be an image.'
        ]);
    }

    public function test_created_profile_has_tags(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $createProfileAttribute = (new ProfileAttribute())->toArray();

        $response = $this->postJson('/api/v2/users/' . $user->id . '/profiles', $createProfileAttribute);

        $response->assertOk();
        $customerProfile = $response->json('customer_profile');
        $customerProfile = CustomerProfile::find($customerProfile['id']);
        $this->assertNotEquals(0, $customerProfile->tags->count());
    }

    public function test_profile_not_created_if_tagging_failed(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $createProfileAttribute = (new ProfileAttribute())->toArray();

        $this->mock(
            TextTagsGenerator::class,
            function (MockInterface $mock) {
                $mock->shouldReceive('generate')
                    ->andThrow(
                        'Exception',
                        'The connection to api.text-tags-generator took to long to respond',
                        500
                    );
            }
        );

        $response = $this->postJson('/api/v2/users/' . $user->id . '/profiles', $createProfileAttribute);

        $response->assertServerError();
        $this->assertDatabaseCount('customer_profiles', 0);
    }
}
