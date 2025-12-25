<?php

namespace Tests\Util\Auth;

use App\Models\User;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Tests\Util\IDataProvider;

class LoginCredentials implements IDataProvider
{
    private $phone;
    private $password;
    private $publicAttributes;

    public function __construct()
    {
        $user = $this->createUser();
        $this->phone = $user->phone;
        $this->password = $user->passwordPlain;
        $this->publicAttributes = [
            'phone',
            'password'
        ];
    }

    private function createUser(): User
    {
        $password = fake()->password();
        $user = User::factory()->create([
            'password' => $password
        ]);
        $user->passwordPlain = $password;
        return $user;
    }

    public function getPhone(): string
    {
        return $this->phone;
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

    private function collectAttributes(): Collection
    {
        return collect([
            'phone' => $this->phone,
            'password' => $this->password
        ]);
    }

    public function replace(array $data): static
    {
        $credentials = $this->collectAttributes();
        $replacedCredentials = $credentials->replace($data);
        $this->replaceAttributes($replacedCredentials->toArray());

        return $this;
    }

    private function replaceAttributes(array $replacers): void
    {
        foreach ($replacers as $key => $value) {
            switch ($key) {
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
