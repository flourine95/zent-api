<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestGhtkConnection extends Command
{
    protected $signature = 'ghtk:test';

    protected $description = 'Test GHTK API connection and configuration';

    public function handle(): int
    {
        $this->info('Testing GHTK API connection...');
        $this->newLine();

        // Check configuration
        if (! config('services.ghtk.api_token')) {
            $this->error('GHTK_API_TOKEN is not configured in .env file');
            $this->line('Please add GHTK_API_TOKEN to your .env file');

            return self::FAILURE;
        }

        if (! config('services.ghtk.partner_code')) {
            $this->error('GHTK_PARTNER_CODE is not configured in .env file');
            $this->line('Please add GHTK_PARTNER_CODE to your .env file');

            return self::FAILURE;
        }

        // Check if provider is configured in database
        $provider = \App\Models\ShippingProvider::where('code', 'ghtk')->first();

        if (! $provider) {
            $this->error('GHTK provider not found in database');
            $this->line('Please run migrations first: php artisan migrate');

            return self::FAILURE;
        }

        if (! $provider->is_active) {
            $this->warn('GHTK provider is inactive in database');
            $this->line('Set is_active = true in shipping_providers table');
        }

        $this->info('Configuration:');
        $this->line('  API Token: '.substr(config('services.ghtk.api_token'), 0, 10).'...');
        $this->line('  Partner Code: '.config('services.ghtk.partner_code'));
        $this->line('  Provider Status: '.($provider->is_active ? 'Active' : 'Inactive'));
        $this->newLine();

        // Test connection
        try {
            $this->info('Testing API authentication...');
            $ghtkService = \App\Services\ShippingProviderFactory::make('ghtk');
            $response = $ghtkService->testConnection();

            if ($response['success']) {
                $this->info('✓ Connection successful!');
                $this->line('  Message: '.($response['message'] ?? 'OK'));

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
