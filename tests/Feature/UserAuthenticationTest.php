<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserAuthenticationTest extends TestCase
{
   public function test_user_has_api_tokens(): void
    {
        Artisan::call("migrate");
        $user = User::factory()->create();
        $user->createToken("my token");

        $tokens = $user->tokens;

        $this->assertNotCount(0, $tokens);
    }
}
