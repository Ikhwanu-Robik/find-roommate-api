<?php

namespace App\Services\Dummies;

use App\Services\TextTagsGenerator;

class DummyTextTagsGenerator extends TextTagsGenerator
{
    public function generate(string $text): array
    {
        switch ($text) {
            case 'i use arch btw':
                return ['arch'];
            case 'i use windows 11':
                return ['windows'];
        }
        return ['no-tag'];
    }
}