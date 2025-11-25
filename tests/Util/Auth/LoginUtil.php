<?php

namespace Tests\Util\Auth;

use App\Models\User;

class LoginUtil
{
    public static function getLoginCredentialsWithout(array $exclusions)
    {
        $loginCredentials = new LoginCredentials();
        return $loginCredentials->exclude($exclusions);
    }

    public static function getLoginCredentialsInvalidate(array $keysToInvalidate)
    {
        $loginCredentials = new LoginCredentials();
        return $loginCredentials->invalidate($keysToInvalidate);
    }

    public static function getIncorrectLoginData()
    {
        $loginCredentials = new LoginCredentials();
        return $loginCredentials->makeIncorrect();
    }
}
