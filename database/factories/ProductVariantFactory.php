<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        $size = fake()->randomElement(['S', 'M', 'L', 'XL']);
        $color = fake()->randomElement(['Red', 'Blue', 'Black', 'White']);

        return [
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
