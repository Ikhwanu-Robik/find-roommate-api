<?php

namespace App\Models;

use App\Models\CustomerProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_profile_id'
    ];

    public function customerProfiles()
    {
        return $this->belongsToMany(CustomerProfile::class);
    }

    public function isInviting(CustomerProfile $customerProfile)
    {
        return $this->whereHas('customerProfiles', function (Builder $query) use ($customerProfile) {
            $query->where('id', $customerProfile->id);
        });
    }
}
