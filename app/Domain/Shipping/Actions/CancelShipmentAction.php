<?php

namespace App\Domain\Shipping\Actions;

use App\Domain\Order\Exceptions\OrderNotFoundException;
use App\Domain\Order\Exceptions\UnauthorizedOrderAccessException;
use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Domain\Shipping\Exceptions\ShipmentCancellationException;
use App\Domain\Shipping\Exceptions\ShipmentNotFoundException;
use App\Domain\Shipping\Repositories\ShipmentRepositoryInterface;

class CancelShipmentAction
{
    public function __construct(
        private readonly ShipmentRepositoryInterface $shipmentRepository,
        private readonly OrderRepositoryInterface $orderRepository,
    ) {}

    /**
     * @throws OrderNotFoundException
     * @throws UnauthorizedOrderAccessException
     * @throws ShipmentNotFoundException
     * @throws ShipmentCancellationException
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

        $cancellableStatuses = ['pending', 'confirmed', 'picking'];

        if (! in_array($shipment['status'], $cancellableStatuses)) {
            throw ShipmentCancellationException::invalidStatus($shipment['status']);
        }

        return $this->shipmentRepository->cancel($shipment['id']);
    }
}
