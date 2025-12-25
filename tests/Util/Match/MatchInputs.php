<?php

namespace Tests\Util\Match;

use App\Models\Lodging;
use InvalidArgumentException;
use Tests\Util\IDataProvider;
use Illuminate\Support\Collection;

class MatchInputs implements IDataProvider
{
    private $gender;
    private $minAge;
    private $maxAge;
    private $lodgingId;
    private $bio;
    private $publicAttributes;

    public function __construct()
    {
        $minAge = fake()->numberBetween(17, 40);
        $maxAge = ++$minAge;

        $this->gender = fake()->randomElement(['male', 'female']);
        $this->minAge = $minAge;
        $this->maxAge = $maxAge;
        $this->lodgingId = Lodging::get()->random(1)->first()->id;
        $this->bio = fake()->realText();
        $this->publicAttributes = [
            'gender',
            'min_age',
            'max_age',
            'lodging_id',
            'bio'
        ];
    }

    private function collectAttributes(): Collection
    {
        return collect([
            'gender' => $this->gender,
            'min_age' => $this->minAge,
            'max_age' => $this->maxAge,
            'lodging_id' => $this->lodgingId,
            'bio' => $this->bio
        ]);
    }

    public function toArray(): array
    {
        return $this->collectAttributes()
            ->only($this->publicAttributes)->toArray();
    }

    public function exclude(array $keys): static
    {
        foreach ($keys as $attr) {
            $idx = array_search($attr, $this->publicAttributes);
            unset($this->publicAttributes[$idx]);
        }

        return $this;
    }

    public function replace(array $data): static
    {
        $inputs = $this->collectAttributes();
        $replacedInputs = $inputs->replace($data);
        $this->replaceAttributes($replacedInputs->toArray());

        return $this;
    }

    private function replaceAttributes(array $replacers): void
    {
        foreach ($replacers as $key => $value) {
            switch ($key) {
                case 'gender':
                    $this->gender = $value;
                    break;
                case 'min_age':
                    $this->minAge = $value;
                    break;
                case 'max_age':
                    $this->maxAge = $value;
                    break;
                case 'lodging_id':
                    $this->lodgingId = $value;
                    break;
                case 'bio':
                    $this->bio = $value;
                    break;
                default:
                    throw new InvalidArgumentException;
            }
        }
    }
}