<?php

namespace App\Infrastructure\ExternalServices;

use App\Domain\Shipping\Contracts\ShippingProviderInterface;
use App\Infrastructure\ExternalServices\Ghn\GhnService;
use App\Infrastructure\ExternalServices\Ghtk\GhtkService;
use App\Infrastructure\Models\ShippingProvider;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class ShippingProviderFactory
{
    /**
     * Create provider instance by code with caching
     */
    public static function make(string $providerCode): ShippingProviderInterface
    {
        // Cache provider config for 5 minutes to avoid repeated DB queries
        $provider = Cache::remember(
            "shipping_provider_$providerCode",
            config('shipping.cache_duration', 300),
            function () use ($providerCode) {
                return ShippingProvider::where('code', $providerCode)
                    ->where('is_active', true)
                    ->firstOrFail();
            }
        );

        $config = $provider->config;

        return match ($providerCode) {
            'ghtk' => new GhtkService(
                apiToken: config('services.ghtk.api_token') ?? $config['credentials']['api_token'] ?? '',
                partnerCode: config('services.ghtk.partner_code') ?? $config['credentials']['partner_code'] ?? '',
                baseUrl: self::getBaseUrl($config, 'ghtk'),
                defaultPickup: $config['default_pickup'] ?? []
            ),
            'ghn' => new GhnService(
                token: config('services.ghn.token') ?? $config['credentials']['token'] ?? '',
                shopId: (int) (config('services.ghn.shop_id') ?? $config['credentials']['shop_id'] ?? 0),
                baseUrl: self::getBaseUrl($config, 'ghn'),
                defaultPickup: $config['default_pickup'] ?? []
            ),
            default => throw new InvalidArgumentException("Unknown shipping provider: $providerCode"),
        };
    }

    /**
     * Get base URL based on environment
     */
    protected static function getBaseUrl(array $config, string $provider): string
    {
        $environment = $config['endpoints']['environment'] ?? 'production';

        return match ($provider) {
            'ghtk' => $environment === 'staging'
                ? ($config['endpoints']['staging_url'] ?? 'https://services-staging.ghtklab.com')
                : ($config['endpoints']['base_url'] ?? 'https://services.giaohangtietkiem.vn'),
            'ghn' => $environment === 'dev'
                ? ($config['endpoints']['dev_url'] ?? 'https://dev-online-gateway.ghn.vn')
                : ($config['endpoints']['base_url'] ?? 'https://online-gateway.ghn.vn'),
            default => $config['endpoints']['base_url'] ?? '',
        };
    }

    /**
     * Create provider instance from model
     */
    public static function makeFromModel(ShippingProvider $provider): ShippingProviderInterface
    {
        return self::make($provider->code);
    }

    /**
     * Get all available providers
     */
    public static function getAvailableProviders(): array
    {
        return [
            'ghtk' => 'Giao Hàng Tiết Kiệm',
            'ghn' => 'Giao Hàng Nhanh',
            // 'viettel' => 'Viettel Post',
            // 'jnt' => 'J&T Express',
        ];
    }

    /**
     * Clear cached provider config
     */
    public static function clearCache(string $providerCode): void
    {
        Cache::forget("shipping_provider_$providerCode");
    }

    /**
     * Clear all provider caches
     */
    public static function clearAllCaches(): void
    {
        foreach (array_keys(self::getAvailableProviders()) as $code) {
            self::clearCache($code);
        }
    }
}
