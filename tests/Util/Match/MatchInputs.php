<?php

namespace Tests\Util\Match;

use App\Models\Lodging;

class MatchInputs
{
    private $data;

    public function __construct()
    {
        $this->data = $this->createData();
    }

    private function createData(): array
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

    public function exclude(array $keys): array
    {
        $filteredData = array_diff_key(
            $this->data,
            array_flip($keys)
        );

        return $filteredData;
    }

    public function replace(array $data): array
    {
        return array_replace($this->data, $data);
    }
}