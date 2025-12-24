<?php

namespace Tests\Util\Match;

trait MatchAssertions
{
    protected function assertSimilarBio(string $bio1, string $bio2): void
    {
        $tagsGenerator = app()->make('App\Services\TextTagsGenerator');
        $bio1Tags = $tagsGenerator->generate($bio1);
        $bio2Tags = $tagsGenerator->generate($bio2);

        $overlapPoint = count(array_intersect($bio1Tags, $bio2Tags));
        
        $similarityThreshold = config('find_match.similarity_threshold');
        $minSameTags = (int) ceil(count($bio1Tags) * $similarityThreshold);

        $this->assertGreaterThanOrEqual(
            $minSameTags,
            $overlapPoint,
            'the expected bio is not similar enough to the expected criterion'
        );
    }
}