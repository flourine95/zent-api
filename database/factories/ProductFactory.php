<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $prefixes = ['Premium', 'Basic', 'Essential', 'Vintage', 'Streetwear'];
        $types = ['T-Shirt', 'Polo', 'Hoodie', 'Jeans', 'Jacket'];
        $brands = ['Coolmate', 'Uniqlo', 'Adidas', 'Nike', 'Zara'];

        $name = fake()->randomElement($prefixes) . ' ' .
            fake()->randomElement($types) . ' ' .
            fake()->randomElement($brands) . ' ' .
            fake()->year();

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'description' => fake()->paragraphs(2, true),
            'thumbnail' => fake()->imageUrl(600, 600, 'fashion'),

            // JSONB Specs: Giả lập thông số kỹ thuật
            'specs' => [
                'brand' => fake()->randomElement($brands),
                'material' => fake()->randomElement(['100% Cotton', 'Polyester', 'Vải Kaki', 'Denim']),
                'origin' => fake()->randomElement(['Vietnam', 'Korea', 'China']),
                'weight' => fake()->numberBetween(200, 500) . 'g'
            ],
            'is_active' => true,
        ];
    }
}
