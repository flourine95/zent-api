<?php

use App\Infrastructure\ExternalServices\Ghn\GhnService;
use App\Infrastructure\ExternalServices\Ghtk\GhtkService;
use App\Infrastructure\ExternalServices\ShippingProviderFactory;
use App\Infrastructure\Models\ShippingProvider;
use Illuminate\Support\Facades\Http;

describe('Shipping Service Integration', function () {
    beforeEach(function () {
        // Create test shipping providers in database
        ShippingProvider::factory()->create([
            'code' => 'ghtk',
            'name' => 'Giao Hàng Tiết Kiệm',
            'is_active' => true,
            'config' => [
                'credentials' => [
                    'api_token' => config('services.ghtk.api_token'),
                    'partner_code' => config('services.ghtk.partner_code'),
                ],
                'endpoints' => [
                    'environment' => 'staging',
                    'staging_url' => 'https://services-staging.ghtklab.com',
                    'base_url' => 'https://services.giaohangtietkiem.vn',
                ],
                'default_pickup' => [],
            ],
        ]);

        ShippingProvider::factory()->create([
            'code' => 'ghn',
            'name' => 'Giao Hàng Nhanh',
            'is_active' => true,
            'config' => [
                'credentials' => [
                    'token' => config('services.ghn.token'),
                    'shop_id' => config('services.ghn.shop_id'),
                ],
                'endpoints' => [
                    'environment' => 'dev',
                    'dev_url' => 'https://dev-online-gateway.ghn.vn',
                    'base_url' => 'https://online-gateway.ghn.vn',
                ],
                'default_pickup' => [],
            ],
        ]);
    });

    describe('GHTK Service', function () {
        it('can test connection', function () {
            if (! config('services.ghtk.api_token')) {
                $this->markTestSkipped('GHTK credentials not configured');
            }

            Http::fake([
                '*/services/authenticated' => Http::response([
                    'success' => true,
                    'message' => 'Authenticated',
                ], 200),
            ]);

            $service = ShippingProviderFactory::make('ghtk');
            $result = $service->testConnection();

            expect($result)->toHaveKey('success')
                ->and($result['success'])->toBeTrue();
        });

        it('can calculate shipping fee', function () {
            if (! config('services.ghtk.api_token')) {
                $this->markTestSkipped('GHTK credentials not configured');
            }

            Http::fake([
                '*/services/shipment/fee*' => Http::response([
                    'success' => true,
                    'fee' => [
                        'name' => 'Giao hàng tiết kiệm',
                        'fee' => 25000,
                        'insurance_fee' => 5000,
                        'delivery_time' => '2-3 ngày',
                    ],
                ], 200),
            ]);

            $service = ShippingProviderFactory::make('ghtk');
            $result = $service->calculateFee([
                'pick_province' => 'Hà Nội',
                'pick_district' => 'Hoàn Kiếm',
                'province' => 'Hồ Chí Minh',
                'district' => 'Quận 1',
                'weight' => 1000,
                'value' => 500000,
            ]);

            expect($result)->toHaveKey('success')
                ->and($result['success'])->toBeTrue()
                ->and($result)->toHaveKey('fee');
        });
    });

    describe('GHN Service', function () {
        it('can calculate shipping fee', function () {
            if (! config('services.ghn.token')) {
                $this->markTestSkipped('GHN credentials not configured');
            }

            Http::fake([
                '*/shiip/public-api/v2/shipping-order/fee' => Http::response([
                    'code' => 200,
                    'message' => 'Success',
                    'data' => [
                        'total' => 30000,
                        'service_fee' => 25000,
                        'insurance_fee' => 5000,
                        'expected_delivery_time' => '2024-12-20T10:00:00',
                    ],
                ], 200),
            ]);

            $service = ShippingProviderFactory::make('ghn');
            $result = $service->calculateFee([
                'from_district_id' => 1542,
                'to_district_id' => 1443,
                'weight' => 1000,
                'insurance_value' => 500000,
                'service_type_id' => 2,
            ]);

            expect($result)->toHaveKey('code')
                ->and($result['code'])->toBe(200)
                ->and($result)->toHaveKey('data');
        });

        it('can get provinces', function () {
            if (! config('services.ghn.token')) {
                $this->markTestSkipped('GHN credentials not configured');
            }

            Http::fake([
                '*/shiip/public-api/master-data/province' => Http::response([
                    'code' => 200,
                    'message' => 'Success',
                    'data' => [
                        ['ProvinceID' => 201, 'ProvinceName' => 'Hà Nội'],
                        ['ProvinceID' => 202, 'ProvinceName' => 'Hồ Chí Minh'],
                    ],
                ], 200),
            ]);

            $service = ShippingProviderFactory::make('ghn');
            $result = $service->getProvinces();

            expect($result)->toHaveKey('code')
                ->and($result['code'])->toBe(200)
                ->and($result['data'])->toBeArray();
        });
    });

    describe('Shipping Provider Factory', function () {
        it('can create GHTK service instance', function () {
            $service = ShippingProviderFactory::make('ghtk');

            expect($service)->toBeInstanceOf(GhtkService::class)
                ->and($service->getProviderCode())->toBe('ghtk');
        });

        it('can create GHN service instance', function () {
            $service = ShippingProviderFactory::make('ghn');

            expect($service)->toBeInstanceOf(GhnService::class)
                ->and($service->getProviderCode())->toBe('ghn');
        });

        it('throws exception for unknown provider', function () {
            ShippingProviderFactory::make('unknown');
        })->throws(InvalidArgumentException::class);
    });

    describe('Status Normalization', function () {
        it('normalizes GHTK statuses correctly', function () {
            $service = ShippingProviderFactory::make('ghtk');

            expect($service->normalizeStatus(12))->toBe('picking')
                ->and($service->normalizeStatus(3))->toBe('picked')
                ->and($service->normalizeStatus(6))->toBe('delivered')
                ->and($service->normalizeStatus(13))->toBe('cancelled');
        });

        it('normalizes GHN statuses correctly', function () {
            $service = ShippingProviderFactory::make('ghn');

            expect($service->normalizeStatus('picking'))->toBe('picking')
                ->and($service->normalizeStatus('picked'))->toBe('picked')
                ->and($service->normalizeStatus('delivered'))->toBe('delivered')
                ->and($service->normalizeStatus('cancel'))->toBe('cancelled');
        });
    });
});
