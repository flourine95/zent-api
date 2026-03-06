<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $productsPerCategory = 100;
        $categories = Category::all();
        $warehouses = Warehouse::all();

        $totalProducts = $categories->count() * $productsPerCategory;

        $this->command->info("📦 Creating {$totalProducts} products with variants and inventory...");
        $progressBar = $this->command->getOutput()->createProgressBar($totalProducts);
        $progressBar->start();

        Product::factory($productsPerCategory)
            ->recycle($categories)
            ->has(
                ProductVariant::factory(8)
                    ->sequence(fn ($sequence) => $this->getVariantSequence($sequence->index))
                    ->afterCreating(function (ProductVariant $variant) use ($warehouses) {
                        $variant->inventories()->create([
                            'warehouse_id' => $warehouses->random()->id,
                            'quantity' => fake()->numberBetween(10, 100),
                            'shelf_location' => 'Kệ '.fake()->randomLetter().'-'.fake()->numberBetween(1, 50),
                        ]);
                    }),
                'variants'
            )
            ->create()
            ->each(function () use ($progressBar) {
                $progressBar->advance();
            });

        $progressBar->finish();
        $this->command->newLine();
    }

    private function getVariantSequence(int $index): array
    {
        $sizes = [
            ['code' => 'S', 'label' => 'Nhỏ', 'price' => 150000],
            ['code' => 'M', 'label' => 'Vừa', 'price' => 200000],
            ['code' => 'L', 'label' => 'Lớn', 'price' => 250000],
            ['code' => 'XL', 'label' => 'Rất lớn', 'price' => 300000],
        ];

        $colors = [
            ['code' => 'Black', 'label' => 'Đen'],
            ['code' => 'White', 'label' => 'Trắng'],
        ];

        $sizeIndex = $index % 4;
        $colorIndex = (int) ($index / 4) % 2;

        $size = $sizes[$sizeIndex];
        $color = $colors[$colorIndex];

        return [
            'sku' => strtoupper(Str::random(3))."-{$color['code']}-{$size['code']}-".Str::random(3),
            'price' => $size['price'],
            'original_price' => $size['price'] * 1.2,
            'images' => ['images/placeholder.svg'],
            'options' => [
                ['attribute' => 'Kích thước', 'value' => $size['label']],
                ['attribute' => 'Màu sắc', 'value' => $color['label']],
            ],
        ];
    }
}
