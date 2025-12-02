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
        return [
            'gender' => 'transgender',
            'age' => -3,
            'address' => null,
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