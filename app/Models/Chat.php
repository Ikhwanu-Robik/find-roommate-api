<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_room_id',
        'message'
    ];

    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class);
    }
}
