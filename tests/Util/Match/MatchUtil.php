<?php

namespace Tests\Util\Match;

use Tests\Util\Match\MatchInputs;

class MatchUtil
{
    public static function getQueryDataWithout(array $exclusions)
    {
        $formData = new MatchInputs();
        return $formData->exclude($exclusions);
    }
}