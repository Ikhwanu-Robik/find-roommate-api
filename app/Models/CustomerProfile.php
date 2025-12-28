<?php

namespace App\Models;

use App\Models\User;
use Spatie\Tags\HasTags;
use App\Services\TextTagsGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerProfile extends Model
{
    use HasFactory, HasTags;

    protected $fillable = [
        'full_name',
        'gender',
        'birthdate',
        'address',
        'bio',
        'profile_photo',
    ];

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    #[Scope]
    public function whereBioLike(Builder $builder, string $bio)
    {
        $tagsGenerator = app()->make(TextTagsGenerator::class);
        $tags = $tagsGenerator->generate($bio);

        $similarityThreshold = config('find_match.bio_similarity_threshold');
        $minSameTags = (int) ceil(count($tags) * $similarityThreshold);

        $builder->whereHas('tags', function (Builder $innerBuilder) use ($tags) {
            $innerBuilder->whereIn('tags.name->' . config('app.locale'),  $tags);
        }, '>=', $minSameTags); 
    }
}
