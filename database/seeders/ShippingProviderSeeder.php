<?php

namespace Database\Seeders;

use App\Models\ShippingProvider;
use Illuminate\Database\Seeder;

class ShippingProviderSeeder extends Seeder
{
    /**
     * Seed shipping providers with default configuration.
     * 
     * Note: Credentials (tokens, API keys) should be set in .env file.
     * This seeder only creates providers with non-sensitive config.
     */
    public function run(): void
    {
        // GHTK - Giao Hàng Tiết Kiệm
        ShippingProvider::updateOrCreate(
            ['code' => 'ghtk'],
            [
                'name' => 'Giao Hàng Tiết Kiệm',
                'is_active' => false, // Admin must configure and activate
                'config' => [
                    'endpoints' => [
                        'base_url' => 'https://services.giaohangtietkiem.vn',
                        'staging_url' => 'https://services-staging.ghtklab.com',
                        'environment' => 'production',
                    ],
                    'default_pickup' => [
                        'name' => '',
                        'address' => '',
                        'province' => '',
                        'district' => '',
                        'ward' => '',
                        'street' => '',
                        'tel' => '',
                        'email' => '',
                    ],
                ],
                'priority' => 1,
            ]
        );

        // GHN - Giao Hàng Nhanh
        ShippingProvider::updateOrCreate(
            ['code' => 'ghn'],
            [
                'name' => 'Giao Hàng Nhanh',
                'is_active' => false, // Admin must configure and activate
                'config' => [
                    'endpoints' => [
                        'base_url' => 'https://online-gateway.ghn.vn',
                        'dev_url' => 'https://dev-online-gateway.ghn.vn',
                        'environment' => 'production',
                    ],
                    'default_pickup' => [
                        'district_id' => '',
                        'ward_code' => '',
                    ],
                ],
                'priority' => 2,
            ]
        );

        $this->command->info('Shipping providers seeded successfully!');
        $this->command->warn('Remember to:');
        $this->command->line('  1. Set credentials in .env file (GHTK_API_TOKEN, GHN_TOKEN, etc.)');
        $this->command->line('  2. Configure default_pickup addresses in database');
        $this->command->line('  3. Set is_active = true when ready');
    }
}
