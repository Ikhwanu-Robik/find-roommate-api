<?php

namespace App\Http\Controllers;

use App\Models\CustomerProfile;
use App\Http\Requests\GetMatchingRequest;

class MatchController extends Controller
{
    public function getMatchingProfiles(GetMatchingRequest $request)
    {
        $gender = $request->validated('gender');
        $minBirthdate = $this->getBirthdateWhereAge($request->validated('min_age'));
        $maxBirthdate = $this->getBirthdateWhereAge($request->validated('max_age'));

        $matchingProfilesByGender = CustomerProfile::where('gender', $gender);
        $matchingProfilesByAge = $matchingProfilesByGender->where('birthdate', '>=', $maxBirthdate)
            ->where('birthdate', '<=', $minBirthdate);

        $matchingProfiles = $matchingProfilesByAge->get();

        $matchingProfiles->sortBy('id');
        return response()->json([
            'matching_profiles' => $matchingProfiles,
        ]);
    }

    private function getBirthdateWhereAge(int $age)
    {
        $birthdate = now()->subYears($age);
        return $birthdate->toDateString();
    }
}
