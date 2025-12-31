<?php

namespace Tests\Feature;

use Event;
use Tests\TestCase;
use App\Models\User;
use App\Events\NewChat;
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

    public function test_user_can_send_message_to_chat_room(): void
    {
        Event::fake();
        
        $user = User::factory()->create();
        $this->actingAs($user);
        $customerProfile = CustomerProfile::factory()->create();
        $customerProfile->user()->save($user);
        
        $chatRoom = ChatRoom::factory()->create();
        $chatRoom->customerProfiles()->saveMany([
            $customerProfile,
            CustomerProfile::factory()->create()
        ]);
        
        $response = $this->postJson('/api/chat-rooms/' . $chatRoom->id . '/chats', [
            'message' => 'Hello World'
        ]);

        $response->assertOk();
        Event::assertDispatched(NewChat::class, function (NewChat $event) {
            return $event->message == 'Hello World';
        });
    }
}
