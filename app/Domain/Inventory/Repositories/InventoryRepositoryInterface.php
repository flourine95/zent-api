<?php

namespace App\Domain\Inventory\Repositories;

interface InventoryRepositoryInterface
{
    public function create(array $data): array;

    public function update(int $id, array $data): array;

    public function delete(int $id): bool;

    public function findById(int $id): ?array;

    public function exists(int $id): bool;

    public function existsForWarehouseAndVariant(int $warehouseId, int $productVariantId): bool;

    public function getByWarehouse(int $warehouseId): array;

    public function getByProductVariant(int $productVariantId): array;

    public function getAll(): array;

    public function getLowStock(int $threshold): array;
}
