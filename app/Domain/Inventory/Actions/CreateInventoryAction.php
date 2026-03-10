<?php

namespace App\Domain\Inventory\Actions;

use App\Domain\Inventory\DataTransferObjects\CreateInventoryData;
use App\Domain\Inventory\Exceptions\DuplicateInventoryException;
use App\Domain\Inventory\Repositories\InventoryRepositoryInterface;

final readonly class CreateInventoryAction
{
    public function __construct(
        private InventoryRepositoryInterface $inventoryRepository
    ) {}

    /**
     * @throws DuplicateInventoryException
     */
    public function execute(CreateInventoryData $data): array
    {
        // Check if inventory already exists for this warehouse + product variant
        if ($this->inventoryRepository->existsForWarehouseAndVariant($data->warehouseId, $data->productVariantId)) {
            throw DuplicateInventoryException::forWarehouseAndVariant($data->warehouseId, $data->productVariantId);
        }

        return $this->inventoryRepository->create($data->toArray());
    }
}
