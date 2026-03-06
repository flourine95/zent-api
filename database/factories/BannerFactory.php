<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Banner>
 */
class BannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->randomElement([
            'Giảm giá mùa hè',
            'Flash Sale cuối tuần',
            'Khuyến mãi đặc biệt',
            'Sản phẩm mới ra mắt',
        ]);

        return [
            'title' => $title,
            'description' => fake()->sentence(10),
            'image' => 'images/placeholder.svg',
            'link' => fake()->randomElement(['/products', '/categories/electronics', '/sale']),
            'button_text' => fake()->randomElement(['Mua ngay', 'Xem thêm', 'Khám phá']),
            'position' => fake()->randomElement(['hero', 'sidebar', 'footer']),
            'order' => fake()->numberBetween(1, 10),
            'is_active' => fake()->boolean(80),
            'start_date' => now()->subDays(fake()->numberBetween(0, 30)),
            'end_date' => now()->addDays(fake()->numberBetween(7, 60)),
        ];
    }
}
