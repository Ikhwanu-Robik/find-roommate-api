<?php

namespace Tests\Util\Profiles;

use InvalidArgumentException;
use Tests\Util\IDataProvider;
use Illuminate\Support\Collection;

class ProfileAttribute implements IDataProvider
{
    private $fullName;
    private $birthdate;
    private $publicAttributes;

    public function __construct()
    {
        $this->fullName = fake()->name();
        $this->birthdate = fake()->date();

        $this->publicAttributes = [
            'full_name',
            'birthdate',
        ];
    }

    public function toArray(): array
    {
        return $this->collectAttributes()
            ->only($this->publicAttributes)
            ->toArray();
    }

    private function collectAttributes(): Collection
    {
        return collect([
            'full_name' => $this->fullName,
            'birthdate' => $this->birthdate,
        ]);
    }

    public function exclude(array $keys): static
    {
        foreach ($keys as $attr) {
            $idx = array_search($attr, $this->publicAttributes);
            unset($this->publicAttributes[$idx]);
        }

        return $this;
    }

    public function only(array $keys): static
    {
        $this->publicAttributes = $keys;
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
                case 'full_name':
                    $this->fullName = $value;
                    break;
                case 'birthdate':
                    $this->birthdate = $value;
                    break;
                default:
                    throw new InvalidArgumentException;
            }
        }
    }
}
