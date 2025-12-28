<?php

namespace App\Services;

abstract class TextTagsGenerator
{
    public abstract function generate(string $text): array;
}