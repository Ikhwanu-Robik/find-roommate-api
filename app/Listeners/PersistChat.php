<?php

namespace App\Listeners;

use App\Events\NewChat;

class PersistChat
{
    public function __construct()
    {
        //
    }

    public function handle(NewChat $event): void
    {
        $chat = $event->chatRoom->chats()->create([
            'message' => $event->message
        ]);

        $event->sender->chat()->save($chat);
    }
}
