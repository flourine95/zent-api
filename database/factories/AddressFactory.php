<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    public function definition(): array
    {
        return [
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
