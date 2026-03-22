<?php

namespace App\Infrastructure\Services;

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
        ?string $orderId = null
    ): Shipment {
        return DB::transaction(function () use ($providerCode, $orderData, $orderId) {
            $provider = ShippingProvider::byCode($providerCode)->firstOrFail();
            $providerService = ShippingProviderFactory::make($providerCode);

            $response = $providerService->createOrder($orderData);

            if (! $response['success']) {
                throw new \Exception($response['message'] ?? 'Failed to create shipment');
            }

            $orderInfo = $response['order'];
            $orderRequest = $orderData['order'];

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
                'weight' => array_sum(array_column($orderData['products'], 'weight')) * 1000,
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
                $adaptedParams = $this->adaptParamsForProvider($provider->code, $params);
                $feeData = $providerService->calculateFee($adaptedParams);

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
            } catch (\Exception) {
                continue;
            }
        }

        usort($results, fn ($a, $b) => $a['total'] <=> $b['total']);

        return $results;
    }

    /**
     * Adapt params for specific provider
     */
    protected function adaptParamsForProvider(string $providerCode, array $params): array
    {
        return match ($providerCode) {
            'ghtk' => [
                'pick_province' => $params['pick_province'],
                'pick_district' => $params['pick_district'],
                'pick_ward' => $params['pick_ward'] ?? null,
                'province' => $params['province'],
                'district' => $params['district'],
                'ward' => $params['ward'] ?? null,
                'weight' => $params['weight'],
                'value' => $params['value'],
                'transport' => $params['transport'] ?? 'road',
            ],
            'ghn' => [
                'from_district_id' => $params['from_district_id'] ?? null,
                'from_ward_code' => $params['from_ward_code'] ?? null,
                'to_district_id' => $params['to_district_id'] ?? null,
                'to_ward_code' => $params['to_ward_code'] ?? null,
                'weight' => $params['weight'],
                'insurance_value' => $params['value'],
                'service_type_id' => 2,
            ],
            default => $params,
        };
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
    public function createShipmentWithCheapestProvider(array $orderData, ?string $orderId = null): Shipment
    {
        $feeParams = $this->extractFeeParams($orderData);
        $fees = $this->compareShippingFees($feeParams);

        if (empty($fees)) {
            throw new \Exception('No shipping providers available');
        }

        return $this->createShipment($fees[0]['provider_code'], $orderData, $orderId);
    }

    /**
     * Create shipment with best provider based on criteria
     */
    public function createShipmentWithBestProvider(
        array $orderData,
        string $criteria = 'cheapest',
        ?string $orderId = null
    ): Shipment {
        $feeParams = $this->extractFeeParams($orderData);
        $fees = $this->compareShippingFees($feeParams);

        if (empty($fees)) {
            throw new \Exception('No shipping providers available');
        }

        $bestProvider = $this->selectBestProvider($fees, $criteria);

        return $this->createShipment($bestProvider['provider_code'], $orderData, $orderId);
    }

    protected function selectBestProvider(array $fees, string $criteria): array
    {
        return match ($criteria) {
            'cheapest' => $fees[0],
            'fastest' => $this->selectFastest($fees),
            'balanced' => $this->selectBalanced($fees),
            default => $fees[0],
        };
    }

    protected function selectFastest(array $fees): array
    {
        usort($fees, fn ($a, $b) => $this->estimateDays($a['estimated_delivery'] ?? '') <=> $this->estimateDays($b['estimated_delivery'] ?? ''));

        return $fees[0];
    }

    protected function selectBalanced(array $fees): array
    {
        $maxFee = max(array_column($fees, 'total'));
        $minFee = min(array_column($fees, 'total'));

        foreach ($fees as &$fee) {
            $normalizedPrice = $maxFee > $minFee
                ? ($fee['total'] - $minFee) / ($maxFee - $minFee)
                : 0;

            $days = $this->estimateDays($fee['estimated_delivery'] ?? '');
            $normalizedSpeed = $days > 0 ? 1 / $days : 1;

            $fee['score'] = (1 - $normalizedPrice) * 0.6 + $normalizedSpeed * 0.4;
        }

        usort($fees, fn ($a, $b) => $b['score'] <=> $a['score']);

        return $fees[0];
    }

    protected function estimateDays(string $delivery): int
    {
        if (preg_match('/(\d+)-(\d+)/', $delivery, $matches)) {
            return (int) $matches[1];
        }

        if (preg_match('/(\d+)/', $delivery, $matches)) {
            return (int) $matches[1];
        }

        return 3;
    }

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

    protected function parseEstimatedTime(string $time): ?string
    {
        try {
            return \Carbon\Carbon::parse($time)->toDateTimeString();
        } catch (\Exception) {
            return null;
        }
    }
}
