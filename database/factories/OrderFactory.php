<?php

namespace Database\Factories;

use App\Infrastructure\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];

        return [
            'code' => 'ORD-'.strtoupper(fake()->bothify('???###')),
            'status' => fake()->randomElement($statuses),
            'payment_status' => fake()->randomElement($paymentStatuses),
            'total_amount' => fake()->randomFloat(2, 100000, 5000000),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
