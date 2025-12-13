<?php

use App\Models\CustomerProfile;
use App\Models\Lodging;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles_listing', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CustomerProfile::class)->unique();
            $table->foreignIdFor(Lodging::class);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles_listing');
    }
};
