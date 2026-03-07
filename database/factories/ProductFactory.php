<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $prefixes = ['Premium', 'Basic', 'Essential', 'Vintage', 'Streetwear'];
        $types = ['Áo thun', 'Áo polo', 'Áo hoodie', 'Quần jeans', 'Áo khoác'];
        $brands = ['Coolmate', 'Uniqlo', 'Adidas', 'Nike', 'Zara'];

        $name = fake()->randomElement($prefixes).' '.
            fake()->randomElement($types).' '.
            fake()->randomElement($brands);

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(8),
            'description' => fake()->sentence(10),
            'thumbnail' => 'images/placeholder.svg',
            'specs' => [
                ['name' => 'Thương hiệu', 'value' => fake()->randomElement($brands)],
                ['name' => 'Chất liệu', 'value' => fake()->randomElement(['100% Cotton', 'Polyester', 'Vải Kaki', 'Denim'])],
                ['name' => 'Xuất xứ', 'value' => fake()->randomElement(['Việt Nam', 'Hàn Quốc', 'Trung Quốc'])],
                ['name' => 'Trọng lượng', 'value' => fake()->numberBetween(200, 500).'g'],
            ],
            'is_active' => true,
        ];
    }
}
