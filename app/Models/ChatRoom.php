<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
}
