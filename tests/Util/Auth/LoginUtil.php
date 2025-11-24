<?php

namespace Tests\Util\Auth;

use App\Models\User;

class LoginUtil
{
    private static $invalidLoginData = [
        'phone' => '+628122908228',
        'password' => null
    ];

    public static function getLoginDataWithout(array $exclusions)
    {
        $data = self::getLoginData();
        return array_diff_key($data, array_flip($exclusions));
    }

    public static function getLoginDataInvalidate(array $invalidKeys)
    {
        $data = self::getLoginData();
        $invalids = self::getInvalidLoginDataByKeys($invalidKeys);
        return self::replaceLoginDataWithInvalids($data, $invalids);
    }

    public static function getIncorrectLoginData()
    {
        $data = self::getLoginData();
        $otherPhone = '0812-2782-8219';
        $data['phone'] = $otherPhone;

        return $data;
    }

    public static function getLoginData()
    {
        $user = self::createUser();
        return [
            'phone' => $user->phone,
            'password' => $user->passwordPlain
        ];
    }

    private static function createUser()
    {
        $phone = '0812-6578-9189';
        $password = '12345678';
        $user = User::factory()->create(['phone' => $phone, 'password' => $password]);
        $user->passwordPlain = $password;
        return $user;
    }

    private static function getInvalidLoginDataByKeys(array $invalidKeys)
    {
        $filtered = [];
        foreach ($invalidKeys as $invalidKey) {
            $filtered[$invalidKey] = self::$invalidLoginData[$invalidKey];
        }
        return $filtered;
    }

    private static function replaceLoginDataWithInvalids($data, $replacings)
    {
        foreach ($replacings as $key => $value) {
            $data[$key] = $value;
        }
        return $data;
    }

}