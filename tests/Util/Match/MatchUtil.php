<?php

namespace Tests\Util\Match;

use App\Models\User;
use Tests\Util\Match\MatchInputs;

class MatchUtil
{
    public static function getQueryDataWithout(array $exclusions)
    {
        $queryData = new MatchInputs();
        return $queryData->exclude($exclusions);
    }

    public static function getQueryDataInvalidate(array $keysToInvalidate)
    {
        $queryData = new MatchInputs();
        return $queryData->invalidate($keysToInvalidate);
    }

    public static function createProfiles($gender, int $numberOfProfiles)
    {
        User::factory()->count($numberOfProfiles)->create(['gender' => $gender]);
        $users = User::where('gender', $gender)->get();
        $users->sortBy('id');
        return $users->toArray();
        // toArray() is necessary because the API response is also an array
    }
}