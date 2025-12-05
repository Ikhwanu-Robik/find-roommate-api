<?php

namespace Tests\Util\Auth\Login;

use App\Models\User;

class LoginCredentials
{
    // TODO: consider splitting $credentials into many attributes
    private $credentials;
    private $invalidCredentials;

    public function __construct()
    {
        $this->credentials = $this->createCredentials();
        $this->invalidCredentials = new InvalidLoginCredentials();
    }

    private function createCredentials()
    {
        $user = $this->createUser();
        $credentials = $this->extractCredentialsFromUser($user);
        return $credentials;
    }

    private function createUser()
    {
        $password = '12345678';
        $user = User::factory()->create([
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
        $filteredInvalidCredentials = $this->invalidCredentials->filterByKeys($keysToInvalidate);
        $credentialsWithInvalids = $this->replaceWithInvalid($filteredInvalidCredentials);

        return $credentialsWithInvalids;
    }

    private function replaceWithInvalid($invalids)
    {
        foreach ($invalids as $key => $value) {
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
        $otherUser = User::factory()->create();
        $this->credentials['phone'] = $otherUser->phone;
        return $this->credentials;
    }
}
