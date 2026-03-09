<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestGhnConnection extends Command
{
    protected $signature = 'ghn:test';

    protected $description = 'Test GHN API connection and configuration';

    public function handle(): int
    {
        $this->info('Testing GHN API connection...');
        $this->newLine();

        // Check configuration
        if (! config('services.ghn.token')) {
            $this->error('GHN_TOKEN is not configured in .env file');
            $this->line('Please add GHN_TOKEN to your .env file');

            return self::FAILURE;
        }

        if (! config('services.ghn.shop_id')) {
            $this->error('GHN_SHOP_ID is not configured in .env file');
            $this->line('Please add GHN_SHOP_ID to your .env file');

            return self::FAILURE;
        }

        // Check if provider is configured in database
        $provider = \App\Models\ShippingProvider::where('code', 'ghn')->first();

        if (! $provider) {
            $this->error('GHN provider not found in database');
            $this->line('Please run migrations first: php artisan migrate');

            return self::FAILURE;
        }

        if (! $provider->is_active) {
            $this->warn('GHN provider is inactive in database');
            $this->line('Set is_active = true in shipping_providers table');
        }

        $this->info('Configuration:');
        $this->line('  Token: '.substr(config('services.ghn.token'), 0, 10).'...');
        $this->line('  Shop ID: '.config('services.ghn.shop_id'));
        $this->line('  Provider Status: '.($provider->is_active ? 'Active' : 'Inactive'));
        $this->newLine();

        // Test connection by getting provinces
        try {
            $this->info('Testing API authentication...');
            $ghnService = \App\Services\ShippingProviderFactory::make('ghn');
            $response = $ghnService->getProvinces();

            if (isset($response['code']) && $response['code'] === 200) {
                $this->info('✓ Connection successful!');
                $provinceCount = count($response['data'] ?? []);
                $this->line("  Found {$provinceCount} provinces");

                return self::SUCCESS;
            }

            $this->error('✗ Connection failed');
            $this->line('  Message: '.($response['message'] ?? 'Unknown error'));

            return self::FAILURE;

        } catch (\Exception $e) {
            $this->error('✗ Connection error: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
