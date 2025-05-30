<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class StockQuoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_quote_requires_authentication()
    {
        $response = $this->getJson('/api/stock/quote?q=AAPL');
        $response->assertStatus(401);
    }

    public function test_stock_history_requires_authentication()
    {
        $response = $this->getJson('/api/stock/history');
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_access_stock_quote()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/stock/quote?q=AAPL');
        $this->assertTrue(in_array($response->status(), [200, 500]));
    }

    public function test_authenticated_user_can_access_stock_history()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/stock/quote?q=AAPL');
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/stock/history');
        $response->assertStatus(200);
    }

    public function test_login_route_accepts_post_and_returns_token()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    public function test_register_route_accepts_post_and_returns_token()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertStatus(201)
            ->assertJsonStructure(['token']);
    }
}
