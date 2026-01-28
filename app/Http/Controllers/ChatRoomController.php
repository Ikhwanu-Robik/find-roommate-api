<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use Illuminate\Http\Request;

class ChatRoomController extends Controller
{
    public function index(Request $request)
    {
        $chatRooms = $request->user()->profile->chatRooms->load('customerProfiles');
        return response()->json(['chat_rooms' => $chatRooms]);
    }

    public function show(Request $request, ChatRoom $chatRoom)
    {
        $sender = $request->user()->profile;
        if (!$chatRoom->isInviting($sender)) {
            return response()->json(
                ['message' => 'You are not invited to this chat room'],
                403
            );
        }

        return response()->json([
            'chat_room' => $chatRoom
        ]);
    }
}
