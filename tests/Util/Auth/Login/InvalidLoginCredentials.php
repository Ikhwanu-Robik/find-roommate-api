<?php

namespace Tests\Util\Auth\Login;

class InvalidLoginCredentials
{
    private $invalidCredentials;

    public function __construct()
    {
        $this->invalidCredentials = $this->createInvalidCredentials();
    }

    private function createInvalidCredentials()
    {
        $invalidCredentials = [
            'phone' => '+628122908228',
            'password' => null
        ];
        return $invalidCredentials;
    }

    public function filterByKeys($keys)
    {
        $filtered = [];
        foreach ($keys as $key) {
            $filtered[$key] = $this->invalidCredentials[$key];
        }
        return $filtered;
    }
}
