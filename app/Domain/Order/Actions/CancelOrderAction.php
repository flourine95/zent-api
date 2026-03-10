<?php

namespace App\Domain\Order\Actions;

use App\Domain\Order\Exceptions\InvalidOrderException;
use App\Domain\Order\Exceptions\OrderNotFoundException;
use App\Domain\Order\Repositories\OrderRepositoryInterface;

final readonly class CancelOrderAction
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}

    /**
     * @throws OrderNotFoundException
     * @throws InvalidOrderException
     */
    public function execute(int $orderId): array
    {
        $order = $this->orderRepository->findById($orderId);

        if ($order === null) {
            throw OrderNotFoundException::withId($orderId);
        }

        // Cannot cancel completed or already cancelled orders
        if (in_array($order['status'], ['completed', 'cancelled'])) {
            throw InvalidOrderException::cannotCancel($order['status']);
        }

        return $this->orderRepository->update($orderId, [
            'status' => 'cancelled',
        ]);
    }
}
