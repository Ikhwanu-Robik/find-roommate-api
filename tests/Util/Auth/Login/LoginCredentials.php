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

    public function exclude(array $keys): array
    {
        $credentials = $this->collectAttributes();
        $filteredCredentials = $credentials->except($keys);

        return $filteredCredentials->toArray();
    }

    private function collectAttributes(): Collection
    {
        return collect([
            'phone' => $this->phone,
            'password' => $this->password
        ]);
    }

    public function replace(array $data): array
    {   
        $credentials = $this->collectAttributes();
        $replacedCredentials = $credentials->replace($data);

        return $replacedCredentials->toArray();
    }
}
