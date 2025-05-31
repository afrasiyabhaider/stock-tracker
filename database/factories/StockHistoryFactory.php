<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockHistory>
 */
class StockHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'symbol' => $this->faker->randomElement(['AAPL', 'GOOGL', 'MSFT', 'AMZN', 'TSLA']),
            'name' => $this->faker->company,
            'open' => $this->faker->randomFloat(2, 50, 2000),
            'high' => $this->faker->randomFloat(2, 50, 2000),
            'low' => $this->faker->randomFloat(2, 50, 2000),
            'close' => $this->faker->randomFloat(2, 50, 2000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
