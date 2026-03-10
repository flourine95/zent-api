<?php

namespace App\Domain\Order\Actions;

use App\Domain\Order\DataTransferObjects\CreateOrderData;
use App\Domain\Order\Exceptions\InvalidOrderException;
use App\Domain\Order\Repositories\OrderRepositoryInterface;

final readonly class CreateOrderAction
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}

    /**
     * @throws InvalidOrderException
     */
    public function execute(CreateOrderData $data): array
    {
        // Validate order has items
        if (empty($data->items)) {
            throw InvalidOrderException::noItems();
        }

        // Validate total amount matches items subtotal
        $calculatedTotal = array_sum(array_column($data->items, 'subtotal'));
        if (abs($calculatedTotal - $data->totalAmount) > 0.01) {
            throw InvalidOrderException::totalMismatch($data->totalAmount, $calculatedTotal);
        }

        // Create order with items
        return $this->orderRepository->create($data->toArray(), $data->items);
    }
}
