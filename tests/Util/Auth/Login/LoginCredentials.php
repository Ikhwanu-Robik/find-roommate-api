<?php

namespace Tests\Util\Auth\Login;

use App\Models\User;
use Illuminate\Support\Collection;

class LoginCredentials
{
    private string $phone;
    private string $password;

    public function __construct()
    {
        $user = $this->createUser();
        $this->phone = $user->phone;
        $this->password = $user->passwordPlain;
    }

    private function createUser(): User
    {
        $password = '12345678';
        $user = User::factory()->create([
            'password' => $password
        ]);
        $user->passwordPlain = $password;
        return $user;
    }

    public function exclude(array $keys): Collection
    {
        $credentials = $this->collectAttributes();
        $filteredCredentials = $credentials->except($keys);

        return $filteredCredentials;
    }

    private function collectAttributes(): Collection
    {
        return collect([
            'phone' => $this->phone,
            'password' => $this->password
        ]);
    }

    public function replaceWith(array $data): Collection
    {   
        $credentials = $this->collectAttributes();
        $replacedCredentials = $credentials->replace($data);

        return $replacedCredentials;
    }

    public function makeIncorrect(): Collection
    {
        $this->setWrongPhone();
        return $this->collectAttributes();
    }

    private function setWrongPhone(): void
    {
        $otherUser = User::factory()->create();
        $this->phone = $otherUser->phone;
    }
}
