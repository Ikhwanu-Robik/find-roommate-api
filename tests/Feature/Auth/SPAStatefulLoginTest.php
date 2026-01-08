<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Util\Auth\LoginCredentials;

class SPAStatefulLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_SPA_can_login_statefully(): void
    {
        $data = (new LoginCredentials)->toArray();

        $response = $this->postJson('/login', $data);
        $response->assertOk();

        $this->getJson('/api/me')->assertOk();
    }

    public function test_SPA_cannot_login_statefully_with_incorrect_credentials(): void
    {
        $data = new LoginCredentials();
        $otherData = new LoginCredentials();
        $data = $data->replace(['phone' => $otherData->getPhone()])->toArray();

        $response = $this->postJson('/login', $data);

        $response->assertUnauthorized();
    }
}
