<?php

namespace App\Domain\Shipping\Actions;

use App\Domain\Order\Exceptions\OrderNotFoundException;
use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Domain\Shipping\DataTransferObjects\CreateShipmentData;
use App\Domain\Shipping\Exceptions\ShipmentAlreadyExistsException;
use App\Domain\Shipping\Repositories\ShipmentRepositoryInterface;

class CreateShipmentAction
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly ShipmentRepositoryInterface $shipmentRepository,
    ) {}

    public function execute(CreateShipmentData $data): array
    {
        $order = $this->orderRepository->findById($data->orderId);

        if ($order === null) {
            throw OrderNotFoundException::withId($data->orderId);
        }

        $existing = $this->shipmentRepository->findByOrderId($data->orderId);

        if ($existing !== null) {
            throw ShipmentAlreadyExistsException::forOrder($data->orderId);
        }

        return $this->shipmentRepository->create([
            'order_id' => $data->orderId,
            'provider_code' => $data->providerCode,
            'order_data' => $data->orderData,
        ]);
    }
}
