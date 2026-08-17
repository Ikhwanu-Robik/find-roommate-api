<?php

namespace Tests\Util\Profiles;

class ProfileUtil
{
    public static function fullURLtoProfilePhotoPath(string $fullUrl): string
    {
        $url = config('filesystems.disks.s3.url');
        $bucket = config('filesystems.disks.s3.bucket');

        $profilePhotoPathOnly = str_replace(
            "$url/$bucket/",
            "",
            $fullUrl
        );
        return $profilePhotoPathOnly;
    }
}