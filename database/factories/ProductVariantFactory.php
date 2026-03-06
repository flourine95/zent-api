<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * NGUYÊN TẮC 1: GIỮ FACTORY SẠCH
     * - KHÔNG gọi Product::factory() để tránh đẻ rác
     * - product_id sẽ được gán tự động qua ->for() hoặc hasVariants()
     *
     * LƯU Ý: Dữ liệu ở đây là FALLBACK khi gọi trực tiếp
     * Thực tế sẽ dùng sequence() từ Seeder để có data logic hơn
     */
    public function definition(): array
    {
        $size = fake()->randomElement(['S', 'M', 'L', 'XL']);
        $color = fake()->randomElement(['Red', 'Blue', 'Black', 'White']);

        return [
            // KHÔNG có product_id - sẽ được gán tự động
            'sku' => strtoupper(Str::random(8)),
            'price' => fake()->randomElement([150000, 200000, 350000, 500000]),
            'original_price' => null,
            'images' => ['images/placeholder.svg'],
            'options' => [
                ['attribute' => 'Kích thước', 'value' => $this->getSizeLabel($size)],
                ['attribute' => 'Màu sắc', 'value' => $this->getColorLabel($color)],
            ],
        ];
    }

    private function getSizeLabel(string $size): string
    {
        return match ($size) {
            'S' => 'Nhỏ',
            'M' => 'Vừa',
            'L' => 'Lớn',
            'XL' => 'Rất lớn',
            default => $size,
        };
    }

    private function getColorLabel(string $color): string
    {
        return match ($color) {
            'Red' => 'Đỏ',
            'Blue' => 'Xanh',
            'Black' => 'Đen',
            'White' => 'Trắng',
            'Green' => 'Xanh lá',
            'Yellow' => 'Vàng',
            default => $color,
        };
    }
}
