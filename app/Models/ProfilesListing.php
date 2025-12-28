<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProfilesListing extends Model
{
    protected $table = 'profiles_listing';

    protected $fillable = [
        'customer_profile_id',
        'lodging_id',
    ];

    public function customerProfile()
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    public function lodging()
    {
        return $this->belongsTo(Lodging::class);
    }

    #[Scope]
    protected function whereBirthdateBetween(
        Builder $query,
        array $dateRange = [
            'min_birthdate' => null,
            'max_birthdate' => null,
        ]
    ) {
        $query->whereHas('customerProfile', function (Builder $queryInner) use ($dateRange) {
            $queryInner->where('birthdate', '>=', $dateRange['max_birthdate'])
                ->where('birthdate', '<=', $dateRange['min_birthdate']);
        });
    }

    #[Scope]
    protected function whereGender(Builder $query, string $gender)
    {
        $query->whereHas('customerProfile', function (Builder $queryInner) use ($gender) {
            $queryInner->where('gender', $gender);
        });
    }

    #[Scope]
    protected function whereBioLike(Builder $query, string $bio)
    {
        $query->whereHas('customerProfile', function (Builder $queryInner) use ($bio) {
            $queryInner->whereBioLike($bio);
        });
    }
}
