<?php

namespace App\Infrastructure\ExternalServices\Ghtk;

use App\Domain\Shipping\Contracts\ShippingProviderInterface;
use App\Infrastructure\Models\Shipment;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GhtkService implements ShippingProviderInterface
{
    /**
     * Create GHTK service instance
     *
     * @param  string  $apiToken  GHTK API token
     * @param  string  $partnerCode  GHTK partner code
     * @param  string  $baseUrl  API base URL
     * @param  array  $defaultPickup  Default pickup address
     */
    public function __construct(
        protected string $apiToken,
        protected string $partnerCode,
        protected string $baseUrl,
        protected array $defaultPickup = []
    ) {}

    protected function client(): PendingRequest
    {
        return Http::withHeaders([
            'Token' => $this->apiToken,
            'X-Client-Source' => $this->partnerCode,
            'Content-Type' => 'application/json',
        ])->baseUrl($this->baseUrl);
    }

    public function createOrder(array $orderData): array
    {
        try {
            $response = $this->client()
                ->post('/services/shipment/order', $orderData);

            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('GHTK Create Order Error', [
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
                ->get('/services/shipment/fee', $params);

            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('GHTK Calculate Fee Error', [
                'message' => $e->getMessage(),
                'params' => $params,
            ]);

            throw $e;
        }
    }

    public function getStatus(string $providerOrderId): array
    {
        try {
            $identifier = "partner_id:$providerOrderId";

            $response = $this->client()
                ->get("/services/shipment/v2/$identifier");

            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('GHTK Get Order Status Error', [
                'message' => $e->getMessage(),
                'order_id' => $providerOrderId,
            ]);

            throw $e;
        }
    }

    public function cancel(string $providerOrderId): bool
    {
        try {
            $identifier = "partner_id:$providerOrderId";

            $response = $this->client()
                ->post("/services/shipment/cancel/$identifier");

            $result = $this->handleResponse($response);

            return $result['success'] ?? false;
        } catch (Exception $e) {
            Log::error('GHTK Cancel Order Error', [
                'message' => $e->getMessage(),
                'order_id' => $providerOrderId,
            ]);

            throw $e;
        }
    }

    public function getPrintLabelUrl(string $labelId): ?string
    {
        return "$this->baseUrl/services/label/$labelId";
    }

    public function normalizeStatus(int|string $providerStatus): string
    {
        return match ((int) $providerStatus) {
            1, 2 => Shipment::STATUS_PENDING,
            12 => Shipment::STATUS_PICKING,
            3 => Shipment::STATUS_PICKED,
            4 => Shipment::STATUS_IN_TRANSIT,
            5 => Shipment::STATUS_DELIVERING,
            6 => Shipment::STATUS_DELIVERED,
            7 => Shipment::STATUS_RETURNING,
            9 => Shipment::STATUS_RETURNED,
            13 => Shipment::STATUS_CANCELLED,
            10 => Shipment::STATUS_LOST,
            default => Shipment::STATUS_PENDING,
        };
    }

    public function getProviderCode(): string
    {
        return 'ghtk';
    }

    /**
     * Create order builder with default pickup info
     */
    public function orderBuilder(): GhtkOrderBuilder
    {
        return GhtkOrderBuilder::make($this->defaultPickup);
    }

    public function testConnection(): array
    {
        try {
            $response = $this->client()
                ->post('/services/authenticated');

            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('GHTK Test Connection Error', [
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function handleResponse(Response $response): array
    {
        $data = $response->json();

        if ($response->status() === 403) {
            throw new Exception('GHTK API Authentication Failed');
        }

        if (! $response->successful()) {
            throw new Exception(
                $data['message'] ?? 'GHTK API Error',
                $response->status()
            );
        }

        return $data;
    }
}
