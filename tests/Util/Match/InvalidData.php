<?php

namespace Tests\Util\Match;

class InvalidData
{
    private $invalidData;

    public function __construct()
    {
        $this->invalidData = $this->createInvalidData();
    }

    private function createInvalidData()
    {
        // TODO: make invalid be able to handle multiple cases
        // for example
        // age is invalid if it's negative integer
        // age is also invalid if it's not an integer
        $startAge = fake()->numberBetween(-100, 16);
        $endAge = 16; // valid start_age from MatchInputs.php will always be 17 or more
        
        return [
            'gender' => 'transgender',
            'min_age' => $startAge,
            'max_age' => $endAge,
            'lodging_id' => -1,
            'bio' => null,
        ];
    }

    public function filterByKeys($keys)
    {
        $filtered = [];
        foreach ($keys as $key) {
            $filtered[$key] = $this->invalidData[$key];
        }
        return $filtered;
    }
}