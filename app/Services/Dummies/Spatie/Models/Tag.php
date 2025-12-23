<?php

namespace App\Services\Dummies\Spatie\Models;

use App\Models\CustomerProfile;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'order_column'
    ];

    public function customerProfiles()
    {
        return $this->morphedByMany(CustomerProfile::class, 'taggable');
    }
}