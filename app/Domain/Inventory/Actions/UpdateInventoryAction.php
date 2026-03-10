<?php

namespace App\Domain\Inventory\Actions;

use App\Domain\Inventory\DataTransferObjects\UpdateInventoryData;
use App\Domain\Inventory\Exceptions\InventoryNotFoundException;
use App\Domain\Inventory\Repositories\InventoryRepositoryInterface;

final readonly class UpdateInventoryAction
{
    public function __construct(
        private InventoryRepositoryInterface $inventoryRepository
    ) {}

    /**
     * @throws InventoryNotFoundException
     */
    public function execute(UpdateInventoryData $data): array
    {
        if (! $this->inventoryRepository->exists($data->id)) {
            throw InventoryNotFoundException::withId($data->id);
        }

        return $this->inventoryRepository->update($data->id, $data->toArray());
    }
}
