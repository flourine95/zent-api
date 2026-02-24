<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannersSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Summer Sale 2024',
                'description' => 'Giảm giá lên đến 50% cho tất cả sản phẩm',
                'image' => 'banners/summer-sale.jpg',
                'link' => '/collections/summer-sale',
                'button_text' => 'Mua ngay',
                'position' => 'home_hero',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'New Arrivals',
                'description' => 'Khám phá bộ sưu tập mới nhất',
                'image' => 'banners/new-arrivals.jpg',
                'link' => '/collections/new-arrivals',
                'button_text' => 'Xem ngay',
                'position' => 'home_hero',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Free Shipping',
                'description' => 'Miễn phí vận chuyển cho đơn hàng trên 500k',
                'image' => 'banners/free-shipping.jpg',
                'link' => null,
                'button_text' => null,
                'position' => 'home_secondary',
                'order' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }

        $this->command->info('✅ Banners seeded successfully!');
    }
}
