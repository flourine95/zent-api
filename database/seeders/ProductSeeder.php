<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        ini_set('memory_limit', '-1');
        DB::disableQueryLog();

        $categories = Category::all();
        $warehouses = Warehouse::all();

        $totalProducts = 10000;
        $chunkSize = 2000;
        $totalChunks = ceil($totalProducts / $chunkSize);

        $this->command->info("📦 Bắt đầu cày {$totalProducts} Products (Kèm Variants & Inventory)...");
        $this->command->info("🔄 Chia làm {$totalChunks} mẻ, mỗi mẻ {$chunkSize} products.");

        for ($i = 0; $i < $totalChunks; $i++) {

            DB::transaction(function () use ($chunkSize, $categories, $warehouses) {

                Product::factory($chunkSize)
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
                    ->create();

            });

            $current = ($i + 1) * $chunkSize;
            if ($current > $totalProducts) $current = $totalProducts;

            $this->command->info("   -> Đã xử lý xong: {$current} / {$totalProducts}");

            gc_collect_cycles();
        }

        $this->command->info("✅ Hoàn tất cực mượt!");
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
            'sku' => strtoupper(Str::random(4))."-{$color['code']}-{$size['code']}-".Str::random(4),
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
