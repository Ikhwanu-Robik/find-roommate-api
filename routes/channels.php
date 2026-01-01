<?php

use App\Models\ChatRoom;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('ChatRooms.{chatRoom}', function ($user, ChatRoom $chatRoom) {
    return $chatRoom->isInviting($user->profile);
});