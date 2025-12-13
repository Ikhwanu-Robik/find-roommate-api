<?php

namespace Tests\Util\Auth\Signup;

class SignupUtil
{
    public static function getSignupAttributesWithout(array $keys): array
    {
        $signupAttributes = new SignupAttributes();
        return $signupAttributes->exclude($keys)->toArray();
    }

    public static function getSignupAttributesInvalidate(array $keys): array
    {
        $signupAtributes = new SignupAttributes();
        $invalidAttributes = (new InvalidSignupAttributes())->only($keys);
        return $signupAtributes->replaceWith($invalidAttributes)->toArray();
    }
}
