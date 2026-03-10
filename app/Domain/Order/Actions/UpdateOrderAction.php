<?php

namespace App\Domain\Order\Actions;

use App\Domain\Order\DataTransferObjects\UpdateOrderData;
use App\Domain\Order\Exceptions\OrderNotFoundException;
use App\Domain\Order\Repositories\OrderRepositoryInterface;

final readonly class UpdateOrderAction
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}

    /**
     * @throws OrderNotFoundException
     */
    public function execute(UpdateOrderData $data): array
    {
        if (! $this->orderRepository->exists($data->id)) {
            throw OrderNotFoundException::withId($data->id);
        }

        return $this->orderRepository->update($data->id, $data->toArray());
    }
}
