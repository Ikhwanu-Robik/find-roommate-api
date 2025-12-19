<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    protected $fillable = [
        'customer_profile_id'
    ];

    public function customerProfiles()
    {
        return $this->belongsToMany(CustomerProfile::class);
    }
}
