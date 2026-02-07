<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Áo Thun', 'Áo Polo', 'Quần Jean', 'Quần Short',
            'Giày Sneaker', 'Phụ Kiện', 'Jacket', 'Hoodie',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'image' => null,
            'description' => fake()->sentence(),
            'is_visible' => true,
        ];
    }
}
