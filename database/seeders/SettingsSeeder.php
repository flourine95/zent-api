<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Shipping
            ['key' => 'shipping_fee', 'value' => '30000', 'type' => 'number', 'group' => 'shipping', 'description' => 'Phí vận chuyển mặc định (VND)'],
            ['key' => 'free_shipping_threshold', 'value' => '500000', 'type' => 'number', 'group' => 'shipping', 'description' => 'Miễn phí ship cho đơn hàng trên (VND)'],
            ['key' => 'shipping_time', 'value' => '2-3 ngày', 'type' => 'string', 'group' => 'shipping', 'description' => 'Thời gian giao hàng dự kiến'],

            // Contact
            ['key' => 'contact_email', 'value' => 'support@zentshop.com', 'type' => 'string', 'group' => 'contact', 'description' => 'Email hỗ trợ'],
            ['key' => 'contact_phone', 'value' => '1900-xxxx', 'type' => 'string', 'group' => 'contact', 'description' => 'Hotline'],
            ['key' => 'contact_address', 'value' => '123 Nguyễn Huệ, Q.1, TP.HCM', 'type' => 'string', 'group' => 'contact', 'description' => 'Địa chỉ'],

            // Social
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/zentshop', 'type' => 'string', 'group' => 'social', 'description' => 'Facebook Page'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/zentshop', 'type' => 'string', 'group' => 'social', 'description' => 'Instagram'],
            ['key' => 'youtube_url', 'value' => 'https://youtube.com/@zentshop', 'type' => 'string', 'group' => 'social', 'description' => 'YouTube Channel'],

            // Payment
            ['key' => 'payment_methods', 'value' => json_encode(['cod', 'bank_transfer', 'momo', 'vnpay']), 'type' => 'json', 'group' => 'payment', 'description' => 'Phương thức thanh toán'],
            ['key' => 'cod_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'payment', 'description' => 'Cho phép COD'],

            // General
            ['key' => 'site_name', 'value' => 'Zent Shop', 'type' => 'string', 'group' => 'general', 'description' => 'Tên website'],
            ['key' => 'site_description', 'value' => 'Nền tảng thương mại điện tử hàng đầu', 'type' => 'string', 'group' => 'general', 'description' => 'Mô tả website'],
            ['key' => 'maintenance_mode', 'value' => 'false', 'type' => 'boolean', 'group' => 'general', 'description' => 'Chế độ bảo trì'],

            // Order
            ['key' => 'order_auto_cancel_minutes', 'value' => '30', 'type' => 'number', 'group' => 'order', 'description' => 'Tự động hủy đơn chưa thanh toán sau (phút)'],
            ['key' => 'inventory_reservation_minutes', 'value' => '30', 'type' => 'number', 'group' => 'order', 'description' => 'Thời gian giữ hàng (phút)'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('✅ Settings seeded successfully!');
    }
}
