<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * NGUYÊN TẮC 1: GIỮ FACTORY SẠCH
     * - KHÔNG gọi User::factory()
     * - user_id sẽ được gán qua ->for() hoặc ->recycle()
     */
    public function definition(): array
    {
        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];

        return [
            // KHÔNG có user_id
            'code' => 'ORD-'.strtoupper(fake()->bothify('???###')),
            'status' => fake()->randomElement($statuses),
            'payment_status' => fake()->randomElement($paymentStatuses),
            'total_amount' => fake()->randomFloat(2, 100000, 5000000),
            'note' => fake()->optional()->sentence(),
        ];
    }
}
