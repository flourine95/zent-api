<?php

namespace App\Domain\Shipping\Actions;

use App\Domain\Order\Exceptions\OrderNotFoundException;
use App\Domain\Order\Exceptions\UnauthorizedOrderAccessException;
use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Domain\Shipping\Exceptions\ShipmentNotFoundException;
use App\Domain\Shipping\Repositories\ShipmentRepositoryInterface;

class GetShipmentAction
{
    public function __construct(
        private readonly ShipmentRepositoryInterface $shipmentRepository,
        private readonly OrderRepositoryInterface $orderRepository,
    ) {}

    /**
     * @throws OrderNotFoundException
     * @throws UnauthorizedOrderAccessException
     * @throws ShipmentNotFoundException
     */
    public function execute(string $orderId, string $userId, bool $isAdmin = false): array
    {
        if (! $this->orderRepository->exists($orderId)) {
            throw OrderNotFoundException::withId($orderId);
        }

        if (! $isAdmin && ! $this->orderRepository->belongsToUser($orderId, $userId)) {
            throw UnauthorizedOrderAccessException::forUser();
        }

        $shipment = $this->shipmentRepository->findByOrderId($orderId);

        if ($shipment === null) {
            throw ShipmentNotFoundException::forOrder($orderId);
        }

        return $shipment;
    }
}
