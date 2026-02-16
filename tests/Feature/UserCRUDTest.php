<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class UserCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_edit_his_data(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $data = [
            '_method' => 'PUT',
            'name' => 'John',
            'phone' => '0812-0000-00001',
            'email' => 'john@no-mail.test'
        ];

        $response = $this->postJson('/api/users/' . $user->id, $data);

        $response->assertOk();
        $updatedUser = $response->json('user');
        $this->assertNotSame($user->name, $updatedUser['name']);
        $this->assertNotSame($user->phone, $updatedUser['phone']);
        $this->assertNotSame($user->email, $updatedUser['email']);
    }
}