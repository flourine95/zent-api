<?php

namespace App\Domain\Order\Actions;

use App\Domain\Inventory\Exceptions\InsufficientStockException;
use App\Domain\Inventory\Repositories\InventoryRepositoryInterface;
use App\Domain\Order\DataTransferObjects\CreateOrderData;
use App\Domain\Order\Exceptions\InvalidOrderException;
use App\Domain\Order\Repositories\OrderRepositoryInterface;

final readonly class CreateOrderAction
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private InventoryRepositoryInterface $inventoryRepository,
    ) {}

    /**
     * @throws InvalidOrderException
     * @throws InsufficientStockException
     */
    public function execute(CreateOrderData $data): array
    {
        if (empty($data->items)) {
            throw InvalidOrderException::noItems();
        }

        $calculatedTotal = array_sum(array_column($data->items, 'subtotal'));
        if (abs($calculatedTotal - $data->totalAmount) > 0.01) {
            throw InvalidOrderException::totalMismatch($data->totalAmount, $calculatedTotal);
        }

        // Check stock availability for all items before creating anything
        foreach ($data->items as $item) {
            if (! $this->inventoryRepository->hasAvailableStock(
                $item['warehouse_id'],
                $item['product_variant_id'],
                $item['quantity']
            )) {
                throw InsufficientStockException::forVariant(
                    $item['product_variant_id'],
                    $item['warehouse_id'],
                    $item['quantity'],
                    0
                );
            }
        }

        return $this->orderRepository->createWithReservations($data->toArray(), $data->items);
    }
}
