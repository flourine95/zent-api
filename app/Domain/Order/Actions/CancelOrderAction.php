<?php

namespace App\Domain\Order\Actions;

use App\Domain\Inventory\Repositories\InventoryRepositoryInterface;
use App\Domain\Order\Exceptions\InvalidOrderException;
use App\Domain\Order\Exceptions\OrderNotFoundException;
use App\Domain\Order\Repositories\OrderRepositoryInterface;

final readonly class CancelOrderAction
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private InventoryRepositoryInterface $inventoryRepository,
    ) {}

    /**
     * @throws OrderNotFoundException
     * @throws InvalidOrderException
     */
    public function execute(string $orderId): array
    {
        $order = $this->orderRepository->findById($orderId);

        if ($order === null) {
            throw OrderNotFoundException::withId($orderId);
        }

        if (in_array($order['status'], ['completed', 'cancelled'])) {
            throw InvalidOrderException::cannotCancel($order['status']);
        }

        $this->inventoryRepository->releaseReservations($orderId);

        return $this->orderRepository->update($orderId, ['status' => 'cancelled']);
    }
}
