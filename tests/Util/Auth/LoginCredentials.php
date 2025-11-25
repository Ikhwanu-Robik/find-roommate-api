<?php

namespace Tests\Util\Auth;

use App\Models\User;

class LoginCredentials
{
    private $user;
    private $credentials;
    private $invalidCredentials;

    public function __construct()
    {
        $user = $this->createUser();
        $this->user = $user;
        $this->credentials = $this->extractCredentialsFromUser($user);
        $this->invalidCredentials = $this->createInvalidCredentials();
    }

    private function createUser()
    {
        $phone = '0812-6578-9189'; // factory doesn't generate phone with this format
        $password = '12345678';
        $user = User::factory()->create([
           'phone' => $phone,
           'password' => $password
        ]);
        $user->passwordPlain = $password;
        return $user;
    }

    private function extractCredentialsFromUser($user)
    {
        $credentials = [
            'phone' => $user->phone,
            'password' => $user->passwordPlain
        ];
        return $credentials;
    }

    private function createInvalidCredentials()
    {
        $invalidLoginCredentials = [
            'phone' => '+628122908228',
            'password' => null
        ];
        return $invalidLoginCredentials;
    }

    public function exclude($exclusions)
    {
        $filteredCredentials = array_diff_key(
            $this->credentials,
            array_flip($exclusions)
        );

        return $filteredCredentials;
    }

    public function invalidate($keysToInvalidate)
    {
        $filteredInvalidCredentials = $this->filterInvalidCredentials($keysToInvalidate);
        $loginCredentialsWithInvalid = $this->replaceWithInvalid($filteredInvalidCredentials);

        return $loginCredentialsWithInvalid;
    }

    private function filterInvalidCredentials($keysToInvalidate)
    {
        $filtered = [];
        foreach ($keysToInvalidate as $key) {
            $filtered[$key] = $this->invalidCredentials[$key];
        }
        return $filtered;
    }

    private function replaceWithInvalid($invalidCredentials)
    {
        foreach ($invalidCredentials as $key => $value) {
            $this->credentials[$key] = $value;
        }
        return $this->credentials;
    }

    public function makeIncorrect()
    {
        return $this->setWrongPhone();
    }

    private function setWrongPhone()
    {
        $wrongPhone = '0829-2893-1920'; // different to phone in createUser()
        $this->credentials['phone'] = $wrongPhone;
        return $this->credentials;
    }
}
