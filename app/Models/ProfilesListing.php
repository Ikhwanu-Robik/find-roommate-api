<?php

namespace App\Models;

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
}
