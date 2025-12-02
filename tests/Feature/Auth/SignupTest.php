<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Tests\Util\Auth\Signup\SignupUtil;
use Tests\Util\DummyFilesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SignupTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_user_can_signup(): void
    {
        $data = SignupUtil::getSignupAttributesWithout([]);

        $response = $this->postJson('/api/signup', $data);

        $response->assertOk();
        $response->assertJsonStructure([
            'user' => [
                'id',
                'name',
                'phone',
                'gender',
                'birthdate',
                'address',
                'bio',
                'profile_photo',
            ]
        ]);
    }

    public function test_signup_require_name(): void
    {
        $data = SignupUtil::getSignupAttributesWithout(['name']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('name');
    }

    public function test_signup_require_phone(): void
    {
        $data = SignupUtil::getSignupAttributesWithout(['phone']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('phone');
    }

    public function test_signup_require_valid_format_phone(): void
    {
        $data = SignupUtil::getSignupAttributesInvalidate(['phone']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('phone');
        $response->assertOnlyJsonValidationErrors([
            'phone' => 'The phone is not an Indonesian phone number of the required format'
        ]);
    }

    public function test_signup_require_password(): void
    {
        $data = SignupUtil::getSignupAttributesWithout(['password']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('password');
    }

    public function test_signup_require_birthdate(): void
    {
        $data = SignupUtil::getSignupAttributesWithout(['birthdate']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('birthdate');
    }

    public function test_signup_require_birthdate_to_be_past_date(): void
    {
        $data = SignupUtil::getSignupAttributesInvalidate(['birthdate']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('birthdate');
        $response->assertJsonValidationErrors([
            'birthdate' => 'The birthdate must be past date'
        ]);
    }

    public function test_signup_require_gender(): void
    {
        $data = SignupUtil::getSignupAttributesWithout(['gender']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('gender');
    }

    public function test_signup_require_binary_gender(): void
    {
        $data = SignupUtil::getSignupAttributesInvalidate(['gender']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('gender');
        $response->assertJsonValidationErrors([
            'gender' => 'The gender must be either male or female'
        ]);
    }

    public function test_signup_require_address(): void
    {
        $data = SignupUtil::getSignupAttributesWithout(['address']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('address');
    }

    public function test_signup_require_bio(): void
    {
        $data = SignupUtil::getSignupAttributesWithout(['bio']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('bio');
    }

    public function test_signup_require_profile_photo(): void
    {
        $data = SignupUtil::getSignupAttributesWithout(['profile_photo']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('profile_photo');
    }

    public function test_signup_require_profile_photo_to_be_image(): void
    {
        $data = SignupUtil::getSignupAttributesInvalidate(['profile_photo']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('profile_photo');
        $response->assertJsonValidationErrors([
            'profile_photo' => 'The profile photo field must be an image.'
        ]);
    }

    public function test_signup_uploaded_image_exists_in_storage(): void
    {
        $data = SignupUtil::getSignupAttributesWithout([]);

        $response = $this->postJson('/api/signup', $data);

        $savedFilePath = $response->json('user.profile_photo');
        Storage::disk('public')->assertExists($savedFilePath);
    }

    public function test_signup_return_additional_message_if_image_storage_fails(): void
    {
        Storage::expects('disk')
            ->with('public')
            ->andReturn(new DummyFilesystem);
        $data = SignupUtil::getSignupAttributesWithout([]);

        $response = $this->postJson('/api/signup', $data);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Signup successful, but image storage failed. You can try again in the profile menu'
        ]);
    }
}