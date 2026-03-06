<?php

namespace Database\Factories;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $price = fake()->randomFloat(2, 50000, 1000000);

        return [
            'quantity' => $quantity,
            'price' => $price,
            'product_snapshot' => [
                'name' => fake()->words(3, true),
                'sku' => fake()->bothify('SKU-###-???'),
                'price' => $price,
            ],
        ];
    }
}
