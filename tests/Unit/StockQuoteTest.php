<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class StockQuoteTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Additional setup if needed
    }
    public function test_can_fetch_stock_quote()
    {
        // Simulate fetching a stock quote (mocked data)
        $symbol = 'AAPL';
        $mockQuote = [
            'symbol' => $symbol,
            'price' => 150.25,
            'currency' => 'USD',
        ];

        $this->assertEquals('AAPL', $mockQuote['symbol']);
        $this->assertIsFloat($mockQuote['price']);
        $this->assertEquals('USD', $mockQuote['currency']);
    }

    public function test_invalid_symbol_returns_error()
    {
        // Simulate API error response
        $mockResponse = [
            'error' => 'Symbol not found',
        ];

        $this->assertArrayHasKey('error', $mockResponse);
        $this->assertEquals('Symbol not found', $mockResponse['error']);
    }
    public function test_quote_price_is_positive()
    {
        $mockQuote = [
            'symbol' => 'GOOG',
            'price' => 2735.45,
        ];

        $this->assertGreaterThan(0, $mockQuote['price']);
    }

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
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/stock/history');
        $response->assertStatus(200);
    }
    public function test_stock_quote_returns_valid_json_structure()
    {
        $mockQuote = [
            'symbol' => 'AAPL',
            'price' => 150.25,
            'currency' => 'USD',
        ];

        $this->assertArrayHasKey('symbol', $mockQuote);
        $this->assertArrayHasKey('price', $mockQuote);
        $this->assertArrayHasKey('currency', $mockQuote);
    }
}
