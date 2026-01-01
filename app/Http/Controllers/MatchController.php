<?php

namespace App\Http\Controllers;

use App\Events\NewChat;
use App\Models\ChatRoom;
use Illuminate\Http\Request;
use App\Models\CustomerProfile;
use App\Models\ProfilesListing;
use App\Http\Requests\JoinListingRequest;
use App\Http\Requests\GetProfilesRecommendationRequest as GetProfilesRecRequest;

class MatchController extends Controller
{
    public function getProfilesRecommendation(GetProfilesRecRequest $request)
    {
        $criteria = $request->validated();
        $matchingProfiles = $this->getMatchingProfiles($criteria);
        
        $userCustomerProfile = $request->user()->profile;
        $userProfileInListing = ProfilesListing::where([
            'customer_profile_id' => $userCustomerProfile->id
        ])->first();
        $matchingProfiles = $matchingProfiles->except([$userProfileInListing->id]);

        $matchingProfiles->sortBy('id');

        return response()->json([
            'matching_profiles' => $matchingProfiles,
        ]);
    }

    private function getMatchingProfiles(array $criteria)
    {
        $matchingProfiles = ProfilesListing::whereGender($criteria['gender'])
            ->whereBirthdateBetween([
                'min_birthdate' => $criteria['min_birthdate'],
                'max_birthdate' => $criteria['max_birthdate'],
            ])
            ->whereBioLike($criteria['bio'])
            ->where('lodging_id', $criteria['lodging_id'])
            ->with(['customerProfile', 'lodging'])->get();

        return $matchingProfiles;
    }

    public function joinListing(JoinListingRequest $request)
    {
        $lodgingId = $request->validated('lodging_id');
        $customerProfile = $request->user()->profile;

        if ($this->profileIsInListing($customerProfile)) {
            $profileInListing = $this->updateProfileInListing($customerProfile, $lodgingId);
        } else {
            $profileInListing = $this->createProfileInListing($customerProfile, $lodgingId);
        }

        return response()->json([
            'profile_in_listing' => $profileInListing
        ]);
    }

    private function profileIsInListing(CustomerProfile $customerProfile)
    {
        return ProfilesListing::where('customer_profile_id', $customerProfile->id)->exists();
    }

    private function updateProfileInListing(CustomerProfile $customerProfile, int $lodgingId)
    {
        $profileInListing = ProfilesListing::where('customer_profile_id', $customerProfile->id)
            ->first();
        $profileInListing->lodging_id = $lodgingId;
        $profileInListing->save();

        return $profileInListing;
    }

    private function createProfileInListing(CustomerProfile $customerProfile, int $lodgingId)
    {
        $profileInListing = ProfilesListing::create([
            'customer_profile_id' => $customerProfile->id,
            'lodging_id' => $lodgingId
        ]);

        return $profileInListing;
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

    public function sendChat(Request $request, ChatRoom $chatRoom)
    {
        $validated = $request->validate([
            'message' => ['required', 'string'],
        ]);
        $sender = $request->user()->profile;
        if (! $chatRoom->isInviting($sender)) {
            return response()->json([], 403);
        }

        $message = $validated['message'];
        
        NewChat::dispatch($chatRoom, $sender, $message);
    }
}
