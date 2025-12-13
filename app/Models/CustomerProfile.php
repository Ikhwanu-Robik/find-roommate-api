<?php

namespace App\Models;

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
        $builder->withAnyTags($tags);
    }
}
