<?php

namespace Tests\Util;

class DummyFilesystem
{
    public function putFile($path, $file, $options = [])
    {
        return false;
    }
}