<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        User::factory()->create([
            'name' => 'Admin Long',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $mainWarehouse = Warehouse::create([
            'name' => 'Kho Tổng TP.HCM',
            'code' => 'WH-HCM-MAIN',
            'address' => 'Số 1 Võ Văn Ngân, Thủ Đức',
            'is_active' => true,
        ]);

        // Tạo thêm kho phụ chơi chơi (để test multi-warehouse sau này)
        Warehouse::factory(2)->create();

        // 4. Tạo Danh mục & Sản phẩm
        // Logic: Tạo 5 danh mục, mỗi danh mục có 10 sản phẩm
        $categories = Category::factory(5)->create();

        foreach ($categories as $category) {

            // Tạo 10 sản phẩm cho mỗi danh mục
            $products = Product::factory(10)->create([
                'category_id' => $category->id,
            ]);

            foreach ($products as $product) {
                $this->createVariantsForProduct($product, $mainWarehouse);
            }
        }

        $this->call([
            RolePermissionSeeder::class,
        ]);
    }

    /**
     * Hàm helper để tạo biến thể "Full Option" cho 1 sản phẩm
     */
    private function createVariantsForProduct($product, $warehouse)
    {
        // Định nghĩa Size & Màu để mix
        $sizes = ['S', 'M', 'L', 'XL'];
        $colors = ['Red', 'Blue', 'Black', 'White'];

        // Random để mỗi sản phẩm có các phối màu khác nhau chút
        // Vd: Áo này chỉ có màu Đỏ/Đen, Áo kia có Xanh/Trắng
        $pickedColors = fake()->randomElements($colors, 2);

        foreach ($pickedColors as $color) {
            foreach ($sizes as $size) {

                // Tạo Variant
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => strtoupper(Str::slug($product->name))."-{$color}-{$size}-".Str::random(3),
                    'price' => fake()->randomElement([150000, 200000, 350000, 500000]),
                    'images' => null, // Null để test fallback

                    // JSONB Options: Vietnamese only
                    'options' => [
                        ['attribute' => 'Kích thước', 'value' => $size === 'S' ? 'Nhỏ' : ($size === 'M' ? 'Vừa' : ($size === 'L' ? 'Lớn' : 'Rất lớn'))],
                        ['attribute' => 'Màu sắc', 'value' => $color === 'Red' ? 'Đỏ' : ($color === 'Blue' ? 'Xanh' : ($color === 'Black' ? 'Đen' : 'Trắng'))],
                    ],
                ]);

                // Nhập kho luôn (Inventory)
                Inventory::create([
                    'warehouse_id' => $warehouse->id, // Nhập vào kho tổng ID 1
                    'product_variant_id' => $variant->id,
                    'quantity' => fake()->numberBetween(0, 50), // Có cái hết hàng (0) để test case hết hàng
                    'shelf_location' => 'Kệ '.fake()->randomLetter().'-'.fake()->numberBetween(1, 10),
                ]);
            }
        }
    }
}
