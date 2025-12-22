<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ChatRoom;
use App\Models\CustomerProfile;

class ChatRoomTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_user_can_initiate_a_chat_room(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        CustomerProfile::factory()->create()->user()->save($user);

        $targetProfile = CustomerProfile::factory()->create();

        $response = $this->postJson('/api/match/profiles/' . $targetProfile->id . '/chat');

        $response->assertOk();
        $this->assertNotEmpty($response->json('chat_room_id'));
    }

    public function test_user_can_join_to_chat_room_with_invitation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);

        $chatRoom = ChatRoom::factory()->create();
        $chatRoom->customerProfiles()->saveMany([
            $customerProfile,
            CustomerProfile::factory()->create()
        ]);

        // $response = $this->postJson('/api/broadcast/auth');
        // $response->assertOk();

        // $response = $this->websocket('ChatRooms.' . $chatRoom->id);
        // $response->assertConnected();
    }

    // public function test_user_cannot_join_to_chat_room_without_invitation(): void
    // {
    //     // create the user making the request
    //     $user = User::factory()->create();
    //     $this->actingAs($user);
    //     $customerProfile = CustomerProfile::factory()->create();
    //     $customerProfile->user()->save($user);

    //     // create a chat room without inviting
    //     // the user making the request
    //     $chatRoom = ChatRoom::factory()->hasProfiles(
    //         CustomerProfile::factory()->create(),
    //         CustomerProfile::factory()->create()
    //     )->create();

    //     $response = $this->getJson('/api/match/chat-rooms/' . $chatRoom->id);

    //     $response->assertUnauthorized();
    // }

    // // how can I test the websocket broadcasting?
    // public function test_user_can_sent_a_chat_in_chat_room(): void
    // {
    //     $user = User::factory()->create();
    //     $this->actingAs($user);
    //     $customerProfile = CustomerProfile::factory()->create();
    //     $customerProfile->user()->save($user);
    //     $chatRoom = ChatRoom::factory()->hasProfiles(
    //         $customerProfile,
    //         CustomerProfile::factory()->create()
    //     )->create();
    //     Event::fake();

    //     $response = $this->postJson('/api/match/chat-rooms/' . $chatRoom->id . '/chat');

    //     $response->assertOk();
    //     Event::assertDispatched(SendingChat::class);
    // }

    // public function test_chat_has_online_status(): void
    // {

    // }
}
