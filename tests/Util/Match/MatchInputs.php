<?php

namespace Tests\Util\Match;

class MatchInputs
{
    private $formData;

    public function __construct()
    {
        $this->formData = $this->createFormData();
    }

    private function createFormData()
    {
        return [
            'gender' => fake()->randomElement(['male', 'female']),
        ];
    }

    public function exclude(array $exclusions)
    {
        $filteredFormData = array_diff_key(
            $this->formData,
            array_flip($exclusions)
        );

        return $this->formatAsQuery($filteredFormData);
    }

    private function formatAsQuery(array $assocArr)
    {
        $queryString = "?";

        foreach ($assocArr as $key => $value) {
            $pair = $key . "=" . $value;
            $queryString .= $pair;
        }

        return $queryString;
    }
}