<?php

namespace Tests\Util\Auth\Login;

use Illuminate\Support\Collection;

class InvalidLoginCredentials
{
    private $phone;
    private $password;

    public function __construct()
    {
        $this->phone = fake()->regexify('/^\+62-08[1-9]{1}\d{1}-{1}\d{4}-\d{2,5}$/');
        $this->password = null;
    }

    public function only(array $keys): array
    {
        $invalidCredentials = $this->collectAttributes();
        return $invalidCredentials->only($keys)->toArray();
    }

    private function collectAttributes(): Collection
    {
        return collect([
            'phone' => $this->phone,
            'password' => $this->password
        ]);
    }
}
