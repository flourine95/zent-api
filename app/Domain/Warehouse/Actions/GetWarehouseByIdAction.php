<?php

namespace App\Domain\Warehouse\Actions;

use App\Domain\Warehouse\Exceptions\WarehouseNotFoundException;
use App\Domain\Warehouse\Repositories\WarehouseRepositoryInterface;

class GetWarehouseByIdAction
{
    public function __construct(
        protected WarehouseRepositoryInterface $warehouseRepository
    ) {}

    public function execute(string $id): array
    {
        $warehouse = $this->warehouseRepository->findById($id);

        if (! $warehouse) {
            throw new WarehouseNotFoundException("Warehouse with ID {$id} not found");
        }

        return $warehouse;
    }
}
