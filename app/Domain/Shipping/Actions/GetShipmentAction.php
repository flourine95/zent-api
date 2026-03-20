<?php

namespace App\Domain\Shipping\Actions;

use App\Domain\Shipping\Exceptions\ShipmentNotFoundException;
use App\Domain\Shipping\Repositories\ShipmentRepositoryInterface;

class GetShipmentAction
{
    public function __construct(
        private readonly ShipmentRepositoryInterface $shipmentRepository,
    ) {}

    public function execute(int $orderId): array
    {
        $shipment = $this->shipmentRepository->findByOrderId($orderId);

        if ($shipment === null) {
            throw ShipmentNotFoundException::forOrder($orderId);
        }

        return $shipment;
    }
}
