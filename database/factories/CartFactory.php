<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    /**
     * NGUYÊN TẮC 1: GIỮ FACTORY SẠCH
     * - KHÔNG gọi User::factory()
     */
    public function definition(): array
    {
        return [
            // KHÔNG có user_id - sẽ được gán qua ->for()
        ];
    }
}
