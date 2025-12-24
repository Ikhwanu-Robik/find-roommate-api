<?php

namespace Tests\Util\Match;

use Carbon\Carbon;

class MatchUtil
{
    public static function getBirthdateWhereAge(int $age): string
    {
        $birthdate = now()->subYears($age);
        return $birthdate->toDateString();
    }

    public static function birthdateToAge(string $birthdate): int
    {
        $birthdate = Carbon::create($birthdate);
        return $birthdate->age;
    }

    public static function extractQueryValue(string $queryString, string $key): string|null
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

    public static function createAttributesFromCriteria(array $criteria): array
    {
        $attributes = self::replaceAgeRangeWithAge($criteria);
        $attributes = self::replaceAgeWithBirthdate($attributes);
        unset($attributes['lodging_id']);
        return $attributes;
    }

    private static function replaceAgeRangeWithAge(array $attributes): array
    {
        $age = fake()->numberBetween($attributes['min_age'], $attributes['max_age']);
        $attributes['age'] = $age;

        unset($attributes['min_age'], $attributes['max_age']);

        return $attributes;
    }

    private static function replaceAgeWithBirthdate(array $properties): array
    {
        $birthdate = self::getBirthdateWhereAge($properties['age']);
        unset($properties['age']);
        $properties['birthdate'] = $birthdate;

        return $properties;
    }
}