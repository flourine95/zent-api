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
     * NGUYÊN TẮC 1: GIỮ FACTORY SẠCH
     * - Chỉ định nghĩa các column thuộc bảng products
     * - KHÔNG gọi Category::factory() để tránh đẻ rác
     * - category_id sẽ được gán qua ->for() hoặc ->recycle() từ Seeder
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
            // KHÔNG có category_id ở đây - sẽ được gán từ Seeder
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(5),
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
