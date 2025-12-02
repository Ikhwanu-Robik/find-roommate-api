<?php

namespace Tests\Util\Match;

use Tests\Util\Match\MatchInputs;

class MatchUtil
{
    public static function getQueryDataWithout(array $exclusions)
    {
        $queryData = new MatchInputs();
        return $queryData->exclude($exclusions);
    }

    public static function getQueryDataInvalidate(array $keysToInvalidate)
    {
        $queryData = new MatchInputs();
        return $queryData->invalidate($keysToInvalidate);
    }
}