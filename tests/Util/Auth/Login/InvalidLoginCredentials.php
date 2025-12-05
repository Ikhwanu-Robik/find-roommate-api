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
        $phoneWrongFormat = fake('ID')->regexify('/^+62-08[1-9]{1}\d{1}-{1}\d{4}-\d{2,5}$/');
        $invalidCredentials = [
            'phone' => $phoneWrongFormat,
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
