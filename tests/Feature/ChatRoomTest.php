<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\CustomerProfile;
use Illuminate\Support\Facades\Event;

class ChatRoomTest extends TestCase
{
    public function test_user_can_initiate_a_chat_room(): void
    {
        $this->actingAs(User::factory()->create());
        $targetProfile = CustomerProfile::factory()->create();

        $response = $this->postJson('/api/match/profiles/' . $targetProfile->id . '/chat');

        $response->assertOk();
        $response->assertJson(['chat_room_id']);
        // or
        // $response->assertRedirect('/api/match/chat/' . $chatRoom->id);
    }


    public function test_user_can_join_to_chat_room_with_invitation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);
        $chatRoom = ChatRoom::factory()->hasProfiles(
            $customerProfile,
            CustomerProfile::factory()->create()
        )->create();

        $response = $this->getJson('/api/match/chat-rooms/' . $chatRoom->id);

        $response->assertOk();
    }

    public function test_user_cannot_join_to_chat_room_without_invitation(): void
    {
        // create the user making the request
        $user = User::factory()->create();
        $this->actingAs($user);
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);

        // create a chat room without inviting
        // the user making the request
        $chatRoom = ChatRoom::factory()->hasProfiles(
            CustomerProfile::factory()->create(),
            CustomerProfile::factory()->create()
        )->create();

        $response = $this->getJson('/api/match/chat-rooms/' . $chatRoom->id);

        $response->assertUnauthorized();
    }

    // how can I test the websocket broadcasting?
    public function test_user_can_sent_a_chat_in_chat_room(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);
        $chatRoom = ChatRoom::factory()->hasProfiles(
            $customerProfile,
            CustomerProfile::factory()->create()
        )->create();
        Event::fake();

        $response = $this->postJson('/api/match/chat-rooms/' . $chatRoom->id . '/chat');

        $response->assertOk();
        Event::assertDispatched(SendingChat::class);
    }

    public function test_chat_has_online_status(): void
    {
        
    }
}
