<?php

namespace App\Http\Controllers;

use App\Models\CustomerProfile;
use App\Http\Requests\GetMatchingRequest;

class MatchController extends Controller
{
    public function getMatchingProfiles(GetMatchingRequest $request)
    {
        $gender = $request->validated('gender');
        $matchingProfiles = CustomerProfile::where('gender', $gender)->get();
        $matchingProfiles->sortBy('id');
        return response()->json([
            'matching_profiles' => $matchingProfiles,
        ]);
    }
}
