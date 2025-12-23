<?php

namespace App\Services\Dummies\Spatie\Tags;

use Str;
use App\Services\Dummies\Spatie\Models\Tag;

trait HasTags
{
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function attachTags(array $tags)
    {
        $eloquentTags = $this->stringTagsToModelCollection($tags);

        foreach ($eloquentTags as $eloquentTag) {
            $eloquentTag->customerProfiles()->save($this);
        }
    }

    private function stringTagsToModelCollection(array $tags)
    {
        $eloquentTags = collect([]);

        foreach ($tags as $tag) {
            $eloquentTag = Tag::where('name->' . config('app.locale'), $tag)->get();

            $eloquentTag = $eloquentTag->isEmpty() ?
                $this->createTagFromString($tag) :
                $eloquentTag[0];

            $eloquentTags->push($eloquentTag);
        }

        return $eloquentTags;
    }

    private function createTagFromString(string $tag)
    {
        return Tag::create([
            'name' => json_encode([
                config('app.locale') => $tag
            ]),
            'slug' => json_encode([
                config('app.locale') => Str::slug($tag)
            ])
        ]);
    }
}