<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\GetMatchingRequest;

class MatchController extends Controller
{
    public function getMatchingProfiles(GetMatchingRequest $request)
    {
        $gender = $request->validated('gender');
        $matchingUsers = User::where('gender', $gender)->get();
        $matchingUsers->sortBy('id');
        return response()->json([
            'matching_profiles' => $matchingUsers,
        ]);
    }
}
