<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use Illuminate\Http\Request;

class ChatRoomController extends Controller
{
    public function index(Request $request)
    {
        $authUserProfile = $request->user()->profile;

        $chatRooms = $request->user()->profile
            ->chatRooms
            ->transform(function (ChatRoom $chatRoom) use ($authUserProfile) {
                $chatRoom->setRelation(
                    'customerProfiles',
                    $chatRoom->customerProfiles()
                        ->where('customer_profiles.id', '!=', $authUserProfile->id)
                        ->get()
                );
                return $chatRoom;
            });

        return response()->json(['chat_rooms' => $chatRooms]);
    }

    public function show(Request $request, ChatRoom $chatRoom)
    {
        $authUserProfile = $request->user()->profile;
        if (!$chatRoom->isInviting($authUserProfile)) {
            return response()->json(
                ['message' => 'You are not invited to this chat room'],
                403
            );
        }

        $chatRoom->setRelation(
            'customerProfiles',
            $chatRoom->customerProfiles()
                ->where('customer_profiles.id', '!=', $authUserProfile->id)
                ->get()
        );

        return response()->json([
            'chat_room' => $chatRoom
        ]);
    }
}
