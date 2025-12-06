<?php

namespace Tests\Util\Match;

use App\Models\CustomerProfile;
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
        CustomerProfile::factory()->count($numberOfProfiles)->create(['gender' => $gender]);
        // we need to do 'get()' manually to ensure the order of attributes
        // is the same with the one returned by the API
        $customerProfiles = CustomerProfile::where('gender', $gender)->get();
        $customerProfiles->sortBy('id');
        return $customerProfiles->toArray();
        // toArray() is necessary because the API response is also an array
    }
}