<?php

namespace App\Domain\Warehouse\Actions;

use App\Domain\Warehouse\Repositories\WarehouseRepositoryInterface;

class GetWarehousesAction
{
    public function __construct(
        protected WarehouseRepositoryInterface $warehouseRepository
    ) {}

    public function execute(bool $activeOnly = false): array
    {
        return $activeOnly
            ? $this->warehouseRepository->getActive()
            : $this->warehouseRepository->getAll();
    }
}
