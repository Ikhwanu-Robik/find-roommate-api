<?php

use App\Models\ChatRoom;
use App\Models\CustomerProfile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_room_customer_profile', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CustomerProfile::class);
            $table->foreignIdFor(ChatRoom::class);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_room_customer_profile');
    }
};
