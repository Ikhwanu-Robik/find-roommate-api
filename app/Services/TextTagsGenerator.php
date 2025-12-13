<?php

namespace App\Services;

abstract class TextTagsGenerator
{
    public abstract function generate(string $text): array;
    // TODO: implement TextTagsGenerator
    // either:
    // 1. Host your own model in Python and access it with API
    // 2. Use TNTSearch library for a heuristics tags (need training)
    // 3. Use PhpNlpTools to make your own model (need training)
    //
    // and don't forget to resolve it in the TextTagsProvider
}