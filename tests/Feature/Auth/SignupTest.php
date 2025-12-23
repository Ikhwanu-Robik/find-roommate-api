<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Tests\Util\DummyFilesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Util\Auth\SignupAttributes;
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
        $data = (new SignupAttributes)->toArray();

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
        $data = (new SignupAttributes)->exclude(['name']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('name');
    }

    public function test_signup_require_phone(): void
    {
        $data = (new SignupAttributes)->exclude(['phone']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('phone');
    }

    public function test_signup_require_valid_format_phone(): void
    {
        $invalidFormatPhone = fake()->regexify('/^\+62-08[1-9]{1}\d{1}-{1}\d{4}-\d{2,5}$/');
        $data = (new SignupAttributes)->replace(['phone' => $invalidFormatPhone]);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('phone');
        $response->assertOnlyJsonValidationErrors([
            'phone' => 'The phone is not an Indonesian phone number of the required format'
        ]);
    }

    public function test_signup_require_password(): void
    {
        $data = (new SignupAttributes)->exclude(['password']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('password');
    }

    public function test_signup_require_birthdate(): void
    {
        $data = (new SignupAttributes)->exclude(['birthdate']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('birthdate');
    }

    public function test_signup_require_birthdate_to_be_past_date(): void
    {
        $data = (new SignupAttributes)->replace(['birthdate' => now()->toDateString()]);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('birthdate');
        $response->assertJsonValidationErrors([
            'birthdate' => 'The birthdate must be past date'
        ]);
    }

    public function test_signup_require_gender(): void
    {
        $data = (new SignupAttributes)->exclude(['gender']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('gender');
    }

    public function test_signup_require_binary_gender(): void
    {
        $data = (new SignupAttributes)->replace(['gender' => 'non-binary']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('gender');
        $response->assertJsonValidationErrors([
            'gender' => 'The gender must be either male or female'
        ]);
    }

    public function test_signup_require_address(): void
    {
        $data = (new SignupAttributes)->exclude(['address']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('address');
    }

    public function test_signup_require_bio(): void
    {
        $data = (new SignupAttributes)->exclude(['bio']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('bio');
    }

    public function test_signup_require_profile_photo(): void
    {
        $data = (new SignupAttributes)->exclude(['profile_photo']);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('profile_photo');
    }

    public function test_signup_require_profile_photo_to_be_image(): void
    {
        $data = (new SignupAttributes)->replace([
            'profile_photo' => UploadedFile::fake()->create(
                'not-image.json',
                20,
                'application/json'
            )
        ]);

        $response = $this->postJson('/api/signup', $data);

        $response->assertStatus(422);
        $response->assertOnlyJsonValidationErrors('profile_photo');
        $response->assertJsonValidationErrors([
            'profile_photo' => 'The profile photo field must be an image.'
        ]);
    }

    public function test_signup_uploaded_image_exists_in_storage(): void
    {
        $data = (new SignupAttributes)->toArray();

        $response = $this->postJson('/api/signup', $data);

        $savedFilePath = $response->json('user.profile_photo');
        Storage::disk('public')->assertExists($savedFilePath);
    }

    public function test_signup_return_additional_message_if_image_storage_fails(): void
    {
        $data = (new SignupAttributes)->toArray();
        Storage::expects('disk')
            ->with('public')
            ->andReturn(new DummyFilesystem);

        $response = $this->postJson('/api/signup', $data);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Signup successful, but image storage failed. You can try again in the profile menu'
        ]);
    }
}