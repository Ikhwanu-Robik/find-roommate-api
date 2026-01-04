<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Lodging;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetLodgingTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_lodgings_doesnt_require_authentication(): void
    {
        $lodgings = Lodging::factory(10)->create();

        $response = $this->getJson('/api/lodgings');

        $response->assertOk();
        $response->assertExactJson([
            'lodgings' => $lodgings->toArray()
        ]);
    }
}
