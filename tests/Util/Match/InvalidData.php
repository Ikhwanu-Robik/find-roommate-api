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
        return [
            'gender' => 'transgender',
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