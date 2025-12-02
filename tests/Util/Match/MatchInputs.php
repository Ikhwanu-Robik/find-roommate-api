<?php

namespace Tests\Util\Match;

use Tests\Util\Match\InvalidData;

class MatchInputs
{
    private $data;
    private $invalidData;

    public function __construct()
    {
        $this->data = $this->createData();
        $this->invalidData = new InvalidData();
    }

    private function createData()
    {
        return [
            'gender' => fake()->randomElement(['male', 'female']),
            'age' => fake()->numberBetween(18, 70),
        ];
    }

    public function exclude(array $exclusions)
    {
        $filteredData = array_diff_key(
            $this->data,
            array_flip($exclusions)
        );

        return $this->formatAsQuery($filteredData);
    }

    public function invalidate(array $keysToInvalidate)
    {
        $filteredInvalidData = $this->invalidData->filterByKeys($keysToInvalidate);
        $dataWithInvalids = $this->replaceWithInvalid($filteredInvalidData);

        return $this->formatAsQuery($dataWithInvalids);
    }

    private function replaceWithInvalid($invalids)
    {
        foreach ($invalids as $key => $value) {
            $this->data[$key] = $value;
        }
        return $this->data;
    }

    private function formatAsQuery(array $assocArr)
    {
        $queryString = "?";

        foreach ($assocArr as $key => $value) {
            $pair = $key . "=" . $value;
            $queryString .= $pair;
            $queryString .= '&';
        }

        // remove trailing &
        $queryString = substr($queryString, 0, strlen($queryString) - 1);

        return $queryString;
    }
}