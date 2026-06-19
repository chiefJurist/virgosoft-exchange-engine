<?php

namespace Database\Factories;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'symbol' => 'BTC',
            'amount' => fake()->randomFloat(8, 1, 1.5),
            'locked_amount' => 0.00
        ];
    }

    public function eth()
    {
        return $this->state(fn(array $attributes) => [
            'symbol' => 'ETH',
            'amount' => fake()->randomFloat(8, 10, 15)
        ]);
    }
}