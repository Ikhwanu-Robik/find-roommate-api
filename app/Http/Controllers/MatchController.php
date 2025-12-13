<?php

namespace App\Http\Controllers;

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
            ->whereBioLike($requestData['bio'])
            ->where('lodging_id', $requestData['lodging_id'])
            ->with(['customerProfile', 'lodging'])->get();

        $matchingProfiles->except($userInListing->toArray());
        $matchingProfiles->sortBy('id');

        return response()->json([
            'matching_profiles' => $matchingProfiles,
        ]);
    }
}
