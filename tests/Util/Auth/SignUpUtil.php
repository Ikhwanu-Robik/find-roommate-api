<?php

namespace Tests\Util\Auth;

class SignupUtil
{
    public static function getSignupAttributesWithout(array $exclusions)
    {
        $signupAttributes = new SignupAttributes();
        return $signupAttributes->exclude($exclusions);
    }

    public static function getSignupAttributesInvalidate(array $invalidKeys)
    {
        $signupAtributes = new SignupAttributes();        
        return $signupAtributes->invalidate($invalidKeys);
    }
}
