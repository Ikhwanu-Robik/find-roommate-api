<?php

namespace App\Services;

use App\Exceptions\TagsGenerationFailed;
use Illuminate\Support\Facades\Http;

class APITextTagsGenerator extends TextTagsGenerator
{
    public function generate(string $text): array
    {
        $url = config('tags_generator.api_url');

        $response = Http::asJson()->post($url, ['text' => $text]);

        if ($response->failed()) {
            throw new TagsGenerationFailed("The API responded with status code: " . $response->status());
        }

        return $response->json();
    }
}