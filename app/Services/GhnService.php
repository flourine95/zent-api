<?php

namespace App\Services;

use App\Contracts\ShippingProviderInterface;
use App\Models\Shipment;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GhnService implements ShippingProviderInterface
{
    /**
     * Create GHN service instance
     *
     * @param  string  $token  GHN API token
     * @param  int  $shopId  GHN shop ID
     * @param  string  $baseUrl  API base URL
     * @param  array  $defaultPickup  Default pickup address
     */
    public function __construct(
        protected string $token,
        protected int $shopId,
        protected string $baseUrl,
        protected array $defaultPickup = []
    ) {}

    protected function client(): PendingRequest
    {
        return Http::withHeaders([
            'Token' => $this->token,
            'ShopId' => (string) $this->shopId,
            'Content-Type' => 'application/json',
        ])->baseUrl($this->baseUrl);
    }

    public function createOrder(array $orderData): array
    {
        try {
            $response = $this->client()
                ->post('/shiip/public-api/v2/shipping-order/create', $orderData);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('GHN Create Order Error', [
                'message' => $e->getMessage(),
                'data' => $orderData,
            ]);

            throw $e;
        }
    }

    public function calculateFee(array $params): array
    {
        try {
            $response = $this->client()
                ->post('/shiip/public-api/v2/shipping-order/fee', $params);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('GHN Calculate Fee Error', [
                'message' => $e->getMessage(),
                'params' => $params,
            ]);

            throw $e;
        }
    }

    public function getStatus(string $providerOrderId): array
    {
        try {
            $response = $this->client()
                ->post('/shiip/public-api/v2/shipping-order/detail', [
                    'order_code' => $providerOrderId,
                ]);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('GHN Get Order Status Error', [
                'message' => $e->getMessage(),
                'order_code' => $providerOrderId,
            ]);

            throw $e;
        }
    }

    public function cancel(string $providerOrderId): bool
    {
        try {
            $response = $this->client()
                ->post('/shiip/public-api/v2/switch-status/cancel', [
                    'order_codes' => [$providerOrderId],
                ]);

            $result = $this->handleResponse($response);

            return $result['code'] === 200;
        } catch (\Exception $e) {
            Log::error('GHN Cancel Order Error', [
                'message' => $e->getMessage(),
                'order_code' => $providerOrderId,
            ]);

            throw $e;
        }
    }

    public function getPrintLabelUrl(string $labelId): ?string
    {
        return "{$this->baseUrl}/shiip/public-api/v2/a5/gen-token?order_codes={$labelId}";
    }

    public function normalizeStatus(int|string $providerStatus): string
    {
        // GHN status mapping
        return match ($providerStatus) {
            'ready_to_pick' => Shipment::STATUS_CONFIRMED,
            'picking' => Shipment::STATUS_PICKING,
            'money_collect_picking' => Shipment::STATUS_PICKING,
            'picked' => Shipment::STATUS_PICKED,
            'storing' => Shipment::STATUS_IN_TRANSIT,
            'transporting' => Shipment::STATUS_IN_TRANSIT,
            'sorting' => Shipment::STATUS_IN_TRANSIT,
            'delivering' => Shipment::STATUS_DELIVERING,
            'money_collect_delivering' => Shipment::STATUS_DELIVERING,
            'delivered' => Shipment::STATUS_DELIVERED,
            'delivery_fail' => Shipment::STATUS_DELIVERING,
            'waiting_to_return' => Shipment::STATUS_RETURNING,
            'return' => Shipment::STATUS_RETURNING,
            'return_transporting' => Shipment::STATUS_RETURNING,
            'return_sorting' => Shipment::STATUS_RETURNING,
            'returning' => Shipment::STATUS_RETURNING,
            'returned' => Shipment::STATUS_RETURNED,
            'cancel' => Shipment::STATUS_CANCELLED,
            'exception' => Shipment::STATUS_LOST,
            'damage' => Shipment::STATUS_LOST,
            'lost' => Shipment::STATUS_LOST,
            default => Shipment::STATUS_PENDING,
        };
    }

    public function getProviderCode(): string
    {
        return 'ghn';
    }

    /**
     * Create order builder with default pickup info
     */
    public function orderBuilder(): GhnOrderBuilder
    {
        return GhnOrderBuilder::make($this->defaultPickup);
    }

    /**
     * Get available services
     */
    public function getServices(int $fromDistrictId, int $toDistrictId): array
    {
        try {
            $response = $this->client()
                ->post('/shiip/public-api/v2/shipping-order/available-services', [
                    'shop_id' => $this->shopId,
                    'from_district' => $fromDistrictId,
                    'to_district' => $toDistrictId,
                ]);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('GHN Get Services Error', [
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Get province list
     */
    public function getProvinces(): array
    {
        try {
            $response = $this->client()
                ->get('/shiip/public-api/master-data/province');

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('GHN Get Provinces Error', [
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Get district list
     */
    public function getDistricts(int $provinceId): array
    {
        try {
            $response = $this->client()
                ->post('/shiip/public-api/master-data/district', [
                    'province_id' => $provinceId,
                ]);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('GHN Get Districts Error', [
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Get ward list
     */
    public function getWards(int $districtId): array
    {
        try {
            $response = $this->client()
                ->post('/shiip/public-api/master-data/ward', [
                    'district_id' => $districtId,
                ]);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('GHN Get Wards Error', [
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function handleResponse(Response $response): array
    {
        $data = $response->json();

        if (! $response->successful()) {
            throw new \Exception(
                $data['message'] ?? 'GHN API Error',
                $response->status()
            );
        }

        // GHN returns code 200 for success
        if (isset($data['code']) && $data['code'] !== 200) {
            throw new \Exception(
                $data['message'] ?? 'GHN API Error',
                $data['code']
            );
        }

        return $data;
    }
}
