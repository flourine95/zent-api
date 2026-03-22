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

    /**
     * Check if enough stock is available for a variant in a specific warehouse.
     * Must use pessimistic lock to prevent race conditions.
     */
    public function hasAvailableStock(int $warehouseId, int $productVariantId, int $quantity): bool;

    public function findAvailableWarehouseForVariant(int $productVariantId, int $quantity): ?int;

    /**
     * Decrement quantity and create a reservation atomically.
     * Must be called inside a DB transaction with pessimistic lock.
     *
     * @return array The created reservation as array
     */
    public function reserveStock(int $warehouseId, int $productVariantId, int $quantity, int $orderId): array;

    /**
     * Release all reservations for a given order and restore inventory quantities.
     */
    public function releaseReservations(int $orderId): void;
}
