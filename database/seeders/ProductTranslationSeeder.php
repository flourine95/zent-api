<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductTranslationSeeder extends Seeder
{
    /**
     * Seed sample products with translations for testing
     */
    public function run(): void
    {
        // Tạo categories trước
        $categories = [
            [
                'name' => [
                    'vi' => 'Thời trang nam',
                    'en' => "Men's Fashion",
                ],
                'slug' => 'mens-fashion',
            ],
            [
                'name' => [
                    'vi' => 'Thời trang nữ',
                    'en' => "Women's Fashion",
                ],
                'slug' => 'womens-fashion',
            ],
            [
                'name' => [
                    'vi' => 'Điện tử',
                    'en' => 'Electronics',
                ],
                'slug' => 'electronics',
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // Tạo sample products
        $products = [
            [
                'category_slug' => 'mens-fashion',
                'name' => [
                    'vi' => 'Áo thun nam cotton',
                    'en' => "Men's Cotton T-Shirt",
                ],
                'slug' => 'mens-cotton-tshirt',
                'description' => [
                    'vi' => 'Áo thun nam chất liệu cotton cao cấp, thoáng mát, thấm hút mồ hôi tốt. Phù hợp mặc hàng ngày.',
                    'en' => 'Premium cotton t-shirt for men, breathable and moisture-wicking. Perfect for everyday wear.',
                ],
                'specs' => [
                    'Brand' => 'Nike',
                    'Material' => '100% Cotton',
                    'Origin' => 'Vietnam',
                ],
                'is_active' => true,
            ],
            [
                'category_slug' => 'mens-fashion',
                'name' => [
                    'vi' => 'Quần jean nam slim fit',
                    'en' => "Men's Slim Fit Jeans",
                ],
                'slug' => 'mens-slim-fit-jeans',
                'description' => [
                    'vi' => 'Quần jean nam dáng slim fit, co giãn nhẹ, ôm vừa vặn. Thiết kế hiện đại, phong cách trẻ trung.',
                    'en' => 'Slim fit jeans with slight stretch, comfortable fit. Modern design with youthful style.',
                ],
                'specs' => [
                    'Brand' => 'Levi\'s',
                    'Material' => '98% Cotton, 2% Elastane',
                    'Fit' => 'Slim',
                ],
                'is_active' => true,
            ],
            [
                'category_slug' => 'womens-fashion',
                'name' => [
                    'vi' => 'Váy maxi nữ',
                    'en' => "Women's Maxi Dress",
                ],
                'slug' => 'womens-maxi-dress',
                'description' => [
                    'vi' => 'Váy maxi nữ dáng dài, chất liệu voan mềm mại, thoáng mát. Thích hợp đi biển hoặc dạo phố.',
                    'en' => 'Long maxi dress with soft chiffon fabric, breathable. Perfect for beach or casual outings.',
                ],
                'specs' => [
                    'Brand' => 'Zara',
                    'Material' => 'Chiffon',
                    'Length' => 'Maxi',
                ],
                'is_active' => true,
            ],
            [
                'category_slug' => 'womens-fashion',
                'name' => [
                    'vi' => 'Áo sơ mi nữ công sở',
                    'en' => "Women's Office Shirt",
                ],
                'slug' => 'womens-office-shirt',
                'description' => [
                    'vi' => 'Áo sơ mi nữ thiết kế công sở thanh lịch, chất liệu kate mềm mại, không nhăn. Dễ phối đồ.',
                    'en' => 'Elegant office shirt for women, soft kate fabric, wrinkle-free. Easy to style.',
                ],
                'specs' => [
                    'Brand' => 'H&M',
                    'Material' => 'Kate Silk',
                    'Style' => 'Office',
                ],
                'is_active' => true,
            ],
            [
                'category_slug' => 'electronics',
                'name' => [
                    'vi' => 'Tai nghe Bluetooth không dây',
                    'en' => 'Wireless Bluetooth Headphones',
                ],
                'slug' => 'wireless-bluetooth-headphones',
                'description' => [
                    'vi' => 'Tai nghe Bluetooth không dây, chống ồn chủ động, pin 30 giờ. Âm thanh Hi-Fi chất lượng cao.',
                    'en' => 'Wireless Bluetooth headphones with active noise cancellation, 30-hour battery. High-quality Hi-Fi sound.',
                ],
                'specs' => [
                    'Brand' => 'Sony',
                    'Battery Life' => '30 hours',
                    'Connectivity' => 'Bluetooth 5.0',
                    'Features' => 'Active Noise Cancellation',
                ],
                'is_active' => true,
            ],
            [
                'category_slug' => 'electronics',
                'name' => [
                    'vi' => 'Chuột không dây gaming',
                    'en' => 'Wireless Gaming Mouse',
                ],
                'slug' => 'wireless-gaming-mouse',
                'description' => [
                    'vi' => 'Chuột gaming không dây, DPI cao 16000, đèn RGB tùy chỉnh. Thiết kế ergonomic thoải mái.',
                    'en' => 'Wireless gaming mouse with 16000 DPI, customizable RGB lighting. Ergonomic design for comfort.',
                ],
                'specs' => [
                    'Brand' => 'Logitech',
                    'DPI' => '16000',
                    'Connectivity' => 'Wireless 2.4GHz',
                    'RGB' => 'Yes',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($products as $productData) {
            $category = Category::where('slug', $productData['category_slug'])->first();

            if ($category) {
                Product::firstOrCreate(
                    ['slug' => $productData['slug']],
                    [
                        'category_id' => $category->id,
                        'name' => $productData['name'],
                        'description' => $productData['description'],
                        'specs' => $productData['specs'],
                        'is_active' => $productData['is_active'],
                    ]
                );
            }
        }

        $this->command->info('✅ Created '.count($products).' sample products with translations');
    }
}
