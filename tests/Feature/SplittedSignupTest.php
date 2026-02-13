<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Tests\Util\Auth\UserData;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SplittedSignupTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_user(): void
    {
        $createUserData = (new UserData)->toArray();

        $response = $this->post("/api/v2/signup", $createUserData);

        $response->assertOk();
        $user = $response->json('user');
        // unsetting them because assertDatabaseHas
        // only supports specific format of timestamps
        unset($user['created_at']);
        unset($user['updated_at']);
        $this->assertDatabaseHas('users', $user);
    }

    public function test_create_user_requires_name(): void
    {
        $createUserData = (new UserData)->exclude(['name'])->toArray();

        $response = $this->post("/api/v2/signup", $createUserData);

        $response->assertOnlyInvalid('name');
    }

    public function test_create_user_requires_phone(): void
    {
        $createUserData = (new UserData)->exclude(['phone'])->toArray();

        $response = $this->post("/api/v2/signup", $createUserData);

        $response->assertOnlyInvalid('phone');
    }

    public function test_create_user_requires_valid_phone(): void
    {
        $createUserData = (new UserData)->exclude(['phone'])->toArray();
        $createUserData['phone'] = '+628123456789';

        $response = $this->post("/api/v2/signup", $createUserData);

        $response->assertOnlyInvalid([
            'phone' => 'The phone is not an Indonesian phone number of the required format'
        ]);
    }

    public function test_create_user_requires_password(): void
    {
        $createUserData = (new UserData)->exclude(['password'])->toArray();

        $response = $this->post("/api/v2/signup", $createUserData);

        $response->assertOnlyInvalid('password');
    }

    public function test_phone_must_be_unique(): void
    {
        $user = User::factory()->create();
        $createUserData = (new UserData)
            ->replace(['name' => $user->name])->toArray();

        $response = $this->post("/api/v2/signup", $createUserData);

        $response->assertStatus(422);
        $response->assertOnlyInvalid([
            'phone' => 'The phone is already taken'
        ]);
    }
}
