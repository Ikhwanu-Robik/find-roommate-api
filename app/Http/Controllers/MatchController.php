<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use Illuminate\Http\Request;
use App\Models\CustomerProfile;
use App\Models\ProfilesListing;
use App\Http\Requests\GetProfilesRecommendationRequest as GetProfilesRecRequest;

class MatchController extends Controller
{
    public function getProfilesRecommendation(GetProfilesRecRequest $request)
    {
        $requestData = $request->validated();

        $userCustomerProfile = $request->user()->profile;

        $userInListing = ProfilesListing::createOrFirst([
            'customer_profile_id' => $userCustomerProfile->id,
            'lodging_id' => $requestData['lodging_id'],
        ]);

        $matchingProfiles = ProfilesListing::whereGender($requestData['gender'])
            ->whereBirthdateBetween([
                'min_birthdate' => $requestData['min_birthdate'],
                'max_birthdate' => $requestData['max_birthdate'],
            ])
            ->where('lodging_id', $requestData['lodging_id'])
            ->with(['customerProfile', 'lodging'])->get();

        $matchingProfiles->except($userInListing->toArray());
        $matchingProfiles->sortBy('id');

        return response()->json([
            'matching_profiles' => $matchingProfiles,
        ]);
    }

    public function initiateChatRoom(Request $request, CustomerProfile $customerProfile)
    {
        $initiatorProfile = $request->user()->profile;
        $targetProfile = $customerProfile;
        $chatRoom = ChatRoom::create();
        $chatRoom->customerProfiles()->save($initiatorProfile);
        $chatRoom->customerProfiles()->save($targetProfile);

        return response()->json([
            'chat_room_id' => $chatRoom->id
        ]);
    }
}
