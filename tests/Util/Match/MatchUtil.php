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

    public static function prepareProfile(array $properties)
    {
        $birthdate = self::getBirthdateWhereAge($properties['age']);
        $profile = self::createProfiles([
            'gender' => $properties['gender'],
            'birthdate' => $birthdate,
        ], 1);
        return $profile;
    }

    private static function getBirthdateWhereAge(int $age)
    {
        $birthdate = now()->subYears($age);
        return $birthdate->toDateString();
    }

    private static function createProfiles(array $properties, int $numberOfProfiles)
    {
        CustomerProfile::factory()->count($numberOfProfiles)->create($properties);

        $customerProfiles = new CustomerProfile;
        foreach ($properties as $key => $value) {
            $customerProfiles->where($key, $value);
        }

        // The order of attributes from 'CustomerProfile::create()' is different to 'get()',
        // and the attributes returned by the API is a return of 'get()',
        // and $customerProfiles will be compared with the API response,
        // so $customerProfiles must be a return of 'get()'
        $customerProfiles = $customerProfiles->get();
        $customerProfiles->sortBy('id');
        return $customerProfiles->toArray();
        // toArray() is necessary because the API response is also an array
    }

    public static function extractQueryValue(string $queryString, string $key)
    {
        // remove the '?' at the beginning of $queryString
        $queryString = substr($queryString, 1);

        $items = explode('&', $queryString);
        $result = null;

        foreach ($items as $item) {
            [$itemKey, $itemValue] = explode('=', $item);
            if ($itemKey === $key) {
                $result = $itemValue;
                break;
            }
        }

        return $result;
    }
}