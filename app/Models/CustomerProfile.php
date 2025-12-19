<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    use HasFactory;

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

    public function chatRooms()
    {
        return $this->belongsToMany(ChatRoom::class);
    }
}
