<?php

namespace App\Domain\Shipping\Actions;

use App\Domain\Shipping\Exceptions\ShipmentCancellationException;
use App\Domain\Shipping\Exceptions\ShipmentNotFoundException;
use App\Domain\Shipping\Repositories\ShipmentRepositoryInterface;

class CancelShipmentAction
{
    public function __construct(
        private readonly ShipmentRepositoryInterface $shipmentRepository,
    ) {}

    public function execute(string $orderId): array
    {
        $shipment = $this->shipmentRepository->findByOrderId($orderId);

        if ($shipment === null) {
            throw ShipmentNotFoundException::forOrder($orderId);
        }

        $cancellableStatuses = ['pending', 'confirmed', 'picking'];

        if (! in_array($shipment['status'], $cancellableStatuses)) {
            throw ShipmentCancellationException::invalidStatus($shipment['status']);
        }

        return $this->shipmentRepository->cancel($shipment['id']);
    }
}
