<?php

namespace App\Http\Controllers;

use App\Models\ProfilesListing;
use App\Http\Requests\GetProfilesRecommendationRequest as GetProfilesRecRequest;
use Illuminate\Database\Eloquent\Builder;

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

        $matchingProfiles = ProfilesListing::whereHas(
            'customerProfile',
            function (Builder $query) use ($requestData) {
                $query->where('gender', $requestData['gender']);
                $query->where('birthdate', '>=', $requestData['max_birthdate'])
                    ->where('birthdate', '<=', $requestData['min_birthdate']);
            }
        )
            ->where('lodging_id', $requestData['lodging_id'])
            ->with(['customerProfile', 'lodging'])->get();

        $matchingProfiles->except($userInListing->toArray());
        $matchingProfiles->sortBy('id');

        return response()->json([
            'matching_profiles' => $matchingProfiles,
        ]);
    }
}
