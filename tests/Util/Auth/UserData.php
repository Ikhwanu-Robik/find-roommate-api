<?php

namespace Tests\Util\Auth;

use InvalidArgumentException;
use Tests\Util\IDataProvider;
use Illuminate\Support\Collection;

class UserData implements IDataProvider
{
    private $name;
    private $phone;
    private $password;
    private $publicAttributes;

    public function __construct()
    {
        $this->name = fake()->name();
        $this->phone = fake()->regexify('/^08[1-9]{1}\d{1}-{1}\d{4}-\d{2,5}$/');
        $this->password = fake()->password();
        $this->publicAttributes = [
            "name",
            "phone",
            "password"
        ];
    }

    public function toArray(): array
    {
        return $this->collectAttributes()
            ->only($this->publicAttributes)->toArray();
    }

    private function collectAttributes(): Collection
    {
        return collect([
            'name' => $this->name,
            'phone' => $this->phone,
            'password' => $this->password,
        ]);
    }

    public function exclude(array $keys): static
    {
        foreach ($keys as $key) {
            $idx = array_search($key, $this->publicAttributes);
            unset($this->publicAttributes[$idx]);
        }

        return $this;
    }

    public function replace(array $data): static
    {
        $attributes = $this->collectAttributes();
        $replacedAttributes = $attributes->replace($data);
        $this->replaceAttributes($replacedAttributes->toArray());

        return $this;
    }

    private function replaceAttributes(array $replacers): void
    {
        foreach ($replacers as $key => $value) {
            switch ($key) {
                case 'name':
                    $this->name = $value;
                    break;
                case 'phone':
                    $this->phone = $value;
                    break;
                case 'password':
                    $this->password = $value;
                    break;
                default:
                    throw new InvalidArgumentException;
            }
        }
    }
}