<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * NGUYÊN TẮC 1: GIỮ FACTORY SẠCH
     * - KHÔNG gọi User::factory()
     */
    public function definition(): array
    {
        return [
            // KHÔNG có user_id
            'label' => fake()->randomElement(['Nhà riêng', 'Văn phòng', 'Nhà bố mẹ', null]),
            'recipient_name' => fake()->name(),
            'phone' => fake()->numerify('09########'),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => fake()->optional()->streetName(),
            'city' => fake()->randomElement(['Hà Nội', 'Hồ Chí Minh', 'Đà Nẵng', 'Hải Phòng', 'Cần Thơ']),
            'state' => fake()->optional()->randomElement(['HN', 'HCM', 'DN', 'HP', 'CT']),
            'postal_code' => fake()->numerify('######'),
            'country' => 'VN',
            'is_default' => false,
        ];
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }
}
