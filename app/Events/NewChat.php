<?php

namespace App\Events;

use App\Models\ChatRoom;
use App\Models\CustomerProfile;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class NewChat implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ChatRoom $chatRoom,
        public CustomerProfile $sender,
        public string $message
    ) {
        //
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('ChatRooms.' . $this->chatRoom->id),
        ];
    }
}
