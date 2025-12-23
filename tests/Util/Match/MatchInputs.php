<?php

namespace Tests\Util\Match;

use App\Models\Lodging;
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
        $minAge = fake()->numberBetween(17, 40);
        $maxAge = ++$minAge;

        return [
            'gender' => fake()->randomElement(['male', 'female']),
            'min_age' => $minAge,
            'max_age' => $maxAge,
            'lodging_id' => Lodging::get()->random(1)->first()->id,
            'bio' => fake()->realText(),
        ];
    }

    public function exclude(array $exclusions)
    {
        $filteredData = array_diff_key(
            $this->data,
            array_flip($exclusions)
        );

        return $filteredData;
    }

    public function invalidate(array $keysToInvalidate)
    {
        $filteredInvalidData = $this->invalidData->filterByKeys($keysToInvalidate);
        $dataWithInvalids = $this->replaceWithInvalid($filteredInvalidData);

        return $dataWithInvalids;
    }

    private function replaceWithInvalid($invalids)
    {
        foreach ($invalids as $key => $value) {
            $this->data[$key] = $value;
        }
        return $this->data;
    }
}