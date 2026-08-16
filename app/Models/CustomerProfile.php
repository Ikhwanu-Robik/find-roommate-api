<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected $appends = ['profile_photo'];

    protected function profilePhoto(): Attribute
    {
        return Attribute::make(
            get: function (mixed $values, array $attributes) {
                $path = $attributes['profile_photo'] ?? null;

                if (!$path) {
                    return null;
                }

                $url = config('filesystems.disks.s3.url');
                $bucket = config('filesystems.disks.s3.bucket');
                return "$url/$bucket/$path";
            }
        );
    }

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    public function chat()
    {
        return $this->morphOne(Chat::class, 'sender');
    }

    public function chatRooms()
    {
        return $this->belongsToMany(ChatRoom::class);
    }

    #[Scope]
    public function whereBioLike(Builder $builder, string $bio)
    {
        $tagsGenerator = app()->make(TextTagsGenerator::class);
        $tags = $tagsGenerator->generate($bio);

        $similarityThreshold = config('find_match.bio_similarity_threshold');
        $minSameTags = (int) ceil(count($tags) * $similarityThreshold);

        $builder->whereHas('tags', function (Builder $innerBuilder) use ($tags) {
            $innerBuilder->whereIn('tags.name->' . config('app.locale'), $tags);
        }, '>=', $minSameTags);
    }
}
