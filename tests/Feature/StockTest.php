<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class StockTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_stock_quote()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/stock?q=IBM');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'symbol',
                'name',
                'open',
                'high',
                'low',
                'close'
            ]);
    }

    public function test_authenticated_user_can_get_history()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        \App\Models\StockHistory::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/history');

        $response->assertStatus(200)
            ->assertJsonStructure([[
                'date',
                'name',
                'symbol',
                'open',
                'high',
                'low',
                'close'
            ]]);
    }
}
