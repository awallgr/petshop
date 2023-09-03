<?php

namespace Database\Factories;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Order;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'products' => json_encode([
                [
                    "product" => Str::uuid(),
                    "quantity" => $this->faker->numberBetween(1, 100)
                ],
            ]),
            'address' => json_encode([
                "billing" => $this->faker->streetAddress,
                "shipping" => $this->faker->streetAddress
            ]),
            'amount' => $this->faker->randomFloat(2, 1, 100)
        ];
    }
}
