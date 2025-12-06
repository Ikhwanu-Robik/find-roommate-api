<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('gender');
            $table->dropColumn('birthdate');
            $table->dropColumn('address');
            $table->dropColumn('bio');
            $table->dropColumn('profile_photo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('gender', ['male', 'female']);
            $table->date('birthdate');
            $table->text('address');
            $table->text('bio');
            $table->text('profile_photo')->nullable();
        });
    }
};
