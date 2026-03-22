<?php

namespace App\Domain\Inventory\Actions;

use App\Domain\Inventory\Exceptions\InsufficientInventoryException;
use App\Domain\Inventory\Exceptions\InventoryNotFoundException;
use App\Domain\Inventory\Repositories\InventoryRepositoryInterface;

final readonly class AdjustInventoryAction
{
    public function __construct(
        private InventoryRepositoryInterface $inventoryRepository
    ) {}

    /**
     * @throws InventoryNotFoundException
     * @throws InsufficientInventoryException
     */
    public function execute(string $inventoryId, int $adjustment, string $reason): array
    {
        $inventory = $this->inventoryRepository->findById($inventoryId);

        if ($inventory === null) {
            throw InventoryNotFoundException::withId($inventoryId);
        }

        $newQuantity = $inventory['quantity'] + $adjustment;

        if ($newQuantity < 0) {
            throw InsufficientInventoryException::forAdjustment($inventoryId, $inventory['quantity'], $adjustment);
        }

        return $this->inventoryRepository->update($inventoryId, [
            'quantity' => $newQuantity,
        ]);
    }
}
