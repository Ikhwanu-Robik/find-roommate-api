<?php

namespace Database\Seeders;

use App\Models\Lodging;
use Illuminate\Database\Seeder;

class LodgingSeeder extends Seeder
{
    public function run(): void
    {
        Lodging::factory()->count(10)->create();
    }
}
