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
        $types = ['Áo thun', 'Áo polo', 'Áo hoodie', 'Quần jeans', 'Áo khoác'];
        $brands = ['Coolmate', 'Uniqlo', 'Adidas', 'Nike', 'Zara'];

        $name = fake()->randomElement($prefixes).' '.
            fake()->randomElement($types).' '.
            fake()->randomElement($brands);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(5),
            'description' => fake()->paragraphs(2, true),
            'thumbnail' => null,
            'specs' => [
                'Thương hiệu' => fake()->randomElement($brands),
                'Chất liệu' => fake()->randomElement(['100% Cotton', 'Polyester', 'Vải Kaki', 'Denim']),
                'Xuất xứ' => fake()->randomElement(['Việt Nam', 'Hàn Quốc', 'Trung Quốc']),
                'Trọng lượng' => fake()->numberBetween(200, 500).'g',
            ],
            'is_active' => true,
        ];
    }
}
