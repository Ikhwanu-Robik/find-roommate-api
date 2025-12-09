<?php

namespace Tests\Util\Auth\Login;

use App\Models\User;
use Illuminate\Support\Collection;

class LoginCredentials
{
    private string $phone;
    private string $password;
    private InvalidLoginCredentials $invalidCredentials;

    public function __construct()
    {
        $user = $this->createUser();
        $this->phone = $user->phone;
        $this->password = $user->passwordPlain;
        $this->invalidCredentials = new InvalidLoginCredentials();
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

    public function invalidate(array $keys): Collection
    {
        $filteredInvalids = $this->invalidCredentials->only($keys);
        
        $credentials = $this->collectAttributes();
        $credentialsWithInvalids = $credentials->replace($filteredInvalids);

        return $credentialsWithInvalids;
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
