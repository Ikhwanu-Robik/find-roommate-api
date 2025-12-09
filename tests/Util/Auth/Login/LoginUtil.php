<?php

namespace Tests\Util\Auth\Login;

class LoginUtil
{
    public static function getLoginCredentialsWithout(array $keys): array
    {
        $loginCredentials = new LoginCredentials();
        return $loginCredentials->exclude($keys)->toArray();
    }

    public static function getLoginCredentialsInvalidate(array $keys): array
    {
        $loginCredentials = new LoginCredentials();
        $invalidCredentials = (new InvalidLoginCredentials())->only($keys);
        return $loginCredentials->replaceWith($invalidCredentials)->toArray();
    }

    public static function getIncorrectLoginData(): array
    {
        $loginCredentials = new LoginCredentials();
        return $loginCredentials->makeIncorrect()->toArray();
    }
}
