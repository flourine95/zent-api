<?php

namespace App\Domain\Shipping\Services;

use App\Infrastructure\ExternalServices\ShippingProviderFactory;
use App\Infrastructure\Models\Shipment;
use App\Infrastructure\Models\ShippingProvider;
use Illuminate\Support\Facades\DB;

class ShippingService
{
    /**
     * Create shipment with specified provider
     */
    public function createShipment(
        string $providerCode,
        array $orderData,
        ?int $orderId = null
    ): Shipment {
        return DB::transaction(function () use ($providerCode, $orderData, $orderId) {
            $provider = ShippingProvider::byCode($providerCode)->firstOrFail();
            $providerService = ShippingProviderFactory::make($providerCode);

            // Create order with provider
            $response = $providerService->createOrder($orderData);

            if (! $response['success']) {
                throw new \Exception($response['message'] ?? 'Failed to create shipment');
            }

            $orderInfo = $response['order'];
            $orderRequest = $orderData['order'];

            // Create shipment record
            $shipment = Shipment::create([
                'order_id' => $orderId,
                'provider_id' => $provider->id,
                'provider_order_id' => $orderRequest['id'],
                'tracking_number' => $orderInfo['tracking_id'] ?? null,
                'label_id' => $orderInfo['label'] ?? null,
                'status' => $providerService->normalizeStatus($orderInfo['status_id'] ?? 1),
                'provider_status' => (string) ($orderInfo['status_id'] ?? 1),
                'fee' => $orderInfo['fee'] ?? null,
                'insurance_fee' => $orderInfo['insurance_fee'] ?? null,
                'cod_amount' => $orderRequest['pick_money'],
                'declared_value' => $orderRequest['value'],
                'weight' => array_sum(array_column($orderData['products'], 'weight')) * 1000, // Convert to grams
                'customer_info' => [
                    'name' => $orderRequest['name'],
                    'tel' => $orderRequest['tel'],
                    'address' => $orderRequest['address'],
                    'province' => $orderRequest['province'],
                    'district' => $orderRequest['district'],
                    'ward' => $orderRequest['ward'] ?? null,
                ],
                'pickup_info' => [
                    'name' => $orderRequest['pick_name'],
                    'tel' => $orderRequest['pick_tel'],
                    'address' => $orderRequest['pick_address'],
                    'province' => $orderRequest['pick_province'],
                    'district' => $orderRequest['pick_district'],
                    'ward' => $orderRequest['pick_ward'] ?? null,
                ],
                'estimated_pickup_at' => isset($orderInfo['estimated_pick_time'])
                    ? $this->parseEstimatedTime($orderInfo['estimated_pick_time'])
                    : null,
                'estimated_delivery_at' => isset($orderInfo['estimated_deliver_time'])
                    ? $this->parseEstimatedTime($orderInfo['estimated_deliver_time'])
                    : null,
                'is_freeship' => ($orderRequest['is_freeship'] ?? '0') === '1',
                'note' => $orderRequest['note'] ?? null,
                'products' => $orderData['products'],
                'provider_metadata' => $response,
            ]);

            // Create initial status history
            $shipment->statusHistories()->create([
                'status' => $shipment->status,
                'provider_status' => $shipment->provider_status,
                'note' => 'Shipment created',
                'created_at' => now(),
            ]);

            return $shipment;
        });
    }

    /**
     * Update shipment status from provider
     */
    public function updateShipmentStatus(Shipment $shipment): Shipment
    {
        $providerService = ShippingProviderFactory::makeFromModel($shipment->provider);
        $response = $providerService->getStatus($shipment->provider_order_id);

        if ($response['success'] && isset($response['order'])) {
            $orderData = $response['order'];
            $newStatus = $providerService->normalizeStatus($orderData['status']);

            if ($newStatus !== $shipment->status) {
                $shipment->updateStatus(
                    $newStatus,
                    (string) $orderData['status'],
                    $orderData['message'] ?? null
                );
            }
        }

        return $shipment->fresh();
    }

    /**
     * Cancel shipment
     */
    public function cancelShipment(Shipment $shipment): bool
    {
        if (! $shipment->isCancellable()) {
            throw new \Exception('Shipment cannot be cancelled in current status');
        }

        $providerService = ShippingProviderFactory::makeFromModel($shipment->provider);
        $success = $providerService->cancel($shipment->provider_order_id);

        if ($success) {
            $shipment->updateStatus(
                Shipment::STATUS_CANCELLED,
                'cancelled',
                'Cancelled by user'
            );
        }

        return $success;
    }

    /**
     * Calculate shipping fee for multiple providers
     */
    public function compareShippingFees(array $params): array
    {
        $results = [];
        $activeProviders = ShippingProvider::active()->get();

        foreach ($activeProviders as $provider) {
            try {
                $providerService = ShippingProviderFactory::makeFromModel($provider);
                $feeData = $providerService->calculateFee($params);

                if ($feeData['success'] ?? false) {
                    $results[] = [
                        'provider_code' => $provider->code,
                        'provider_name' => $provider->name,
                        'fee' => $feeData['fee']['fee'] ?? 0,
                        'insurance_fee' => $feeData['fee']['insurance_fee'] ?? 0,
                        'total' => ($feeData['fee']['fee'] ?? 0) + ($feeData['fee']['insurance_fee'] ?? 0),
                        'estimated_delivery' => $feeData['fee']['estimated_delivery'] ?? null,
                    ];
                }
            } catch (\Exception $e) {
                // Skip provider if error
                continue;
            }
        }

        // Sort by total fee
        usort($results, fn ($a, $b) => $a['total'] <=> $b['total']);

        return $results;
    }

    /**
     * Get print label URL
     */
    public function getPrintLabelUrl(Shipment $shipment): ?string
    {
        if (! $shipment->label_id) {
            return null;
        }

        $providerService = ShippingProviderFactory::makeFromModel($shipment->provider);

        return $providerService->getPrintLabelUrl($shipment->label_id);
    }

    /**
     * Create shipment with cheapest provider
     */
    public function createShipmentWithCheapestProvider(
        array $orderData,
        ?int $orderId = null
    ): Shipment {
        // Calculate fees for all providers
        $feeParams = $this->extractFeeParams($orderData);
        $fees = $this->compareShippingFees($feeParams);

        if (empty($fees)) {
            throw new \Exception('No shipping providers available');
        }

        // Use cheapest provider
        $cheapest = $fees[0];

        return $this->createShipment($cheapest['provider_code'], $orderData, $orderId);
    }

    /**
     * Create shipment with best provider based on criteria
     */
    public function createShipmentWithBestProvider(
        array $orderData,
        string $criteria = 'cheapest',
        ?int $orderId = null
    ): Shipment {
        $feeParams = $this->extractFeeParams($orderData);
        $fees = $this->compareShippingFees($feeParams);

        if (empty($fees)) {
            throw new \Exception('No shipping providers available');
        }

        $bestProvider = $this->selectBestProvider($fees, $criteria);

        return $this->createShipment($bestProvider['provider_code'], $orderData, $orderId);
    }

    /**
     * Select best provider based on criteria
     */
    protected function selectBestProvider(array $fees, string $criteria): array
    {
        return match ($criteria) {
            'cheapest' => $fees[0], // Already sorted by price
            'fastest' => $this->selectFastest($fees),
            'balanced' => $this->selectBalanced($fees),
            default => $fees[0],
        };
    }

    /**
     * Select fastest provider
     */
    protected function selectFastest(array $fees): array
    {
        usort($fees, function ($a, $b) {
            $daysA = $this->estimateDays($a['estimated_delivery'] ?? '');
            $daysB = $this->estimateDays($b['estimated_delivery'] ?? '');

            return $daysA <=> $daysB;
        });

        return $fees[0];
    }

    /**
     * Select balanced provider (price + speed)
     */
    protected function selectBalanced(array $fees): array
    {
        $maxFee = max(array_column($fees, 'total'));
        $minFee = min(array_column($fees, 'total'));

        foreach ($fees as &$fee) {
            // Normalize price (0-1)
            $normalizedPrice = $maxFee > $minFee
                ? ($fee['total'] - $minFee) / ($maxFee - $minFee)
                : 0;

            // Estimate days
            $days = $this->estimateDays($fee['estimated_delivery'] ?? '');
            $normalizedSpeed = $days > 0 ? 1 / $days : 1;

            // Score: 60% price, 40% speed
            $fee['score'] = (1 - $normalizedPrice) * 0.6 + $normalizedSpeed * 0.4;
        }

        usort($fees, fn ($a, $b) => $b['score'] <=> $a['score']);

        return $fees[0];
    }

    /**
     * Estimate delivery days from string
     */
    protected function estimateDays(string $delivery): int
    {
        // Parse "1-2 ngày", "2-3 days", etc.
        if (preg_match('/(\d+)-(\d+)/', $delivery, $matches)) {
            return (int) $matches[1]; // Use minimum days
        }

        if (preg_match('/(\d+)/', $delivery, $matches)) {
            return (int) $matches[1];
        }

        return 3; // Default 3 days
    }

    /**
     * Extract fee calculation params from order data
     */
    protected function extractFeeParams(array $orderData): array
    {
        $order = $orderData['order'];

        return [
            'pick_province' => $order['pick_province'],
            'pick_district' => $order['pick_district'],
            'province' => $order['province'],
            'district' => $order['district'],
            'weight' => array_sum(array_column($orderData['products'], 'weight')) * 1000,
            'value' => $order['value'],
            'transport' => $order['transport'] ?? 'road',
        ];
    }
}
