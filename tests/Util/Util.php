<?php

namespace Tests\Util;

use Illuminate\Support\Facades\Artisan;

class Util
{
    public static function setupDatabase()
    {
        Artisan::call('migrate');
    }
}