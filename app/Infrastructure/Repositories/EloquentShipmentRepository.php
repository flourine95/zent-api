<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Shipping\Repositories\ShipmentRepositoryInterface;
use App\Infrastructure\Models\Shipment;
use App\Infrastructure\Services\ShippingService;

final class EloquentShipmentRepository implements ShipmentRepositoryInterface
{
    public function __construct(
        private readonly ShippingService $shippingService,
    ) {}

    public function findByOrderId(string $orderId): ?array
    {
        $shipment = Shipment::with(['provider', 'statusHistories'])
            ->where('order_id', $orderId)
            ->first();

        return $shipment?->toArray();
    }

    public function create(array $data): array
    {
        $shipment = $this->shippingService->createShipment(
            providerCode: $data['provider_code'],
            orderData: $data['order_data'],
            orderId: $data['order_id'],
        );

        return $shipment->load(['provider', 'statusHistories'])->toArray();
    }

    public function updateStatus(string $shipmentId, string $status, ?string $providerStatus, ?string $note): array
    {
        $shipment = Shipment::findOrFail($shipmentId);
        $shipment->updateStatus($status, $providerStatus, $note);

        return $shipment->fresh(['provider', 'statusHistories'])->toArray();
    }

    public function cancel(string $shipmentId): array
    {
        $shipment = Shipment::with('provider')->findOrFail($shipmentId);
        $this->shippingService->cancelShipment($shipment);

        return $shipment->fresh(['provider', 'statusHistories'])->toArray();
    }
}
