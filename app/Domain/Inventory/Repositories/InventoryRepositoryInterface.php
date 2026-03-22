<?php

namespace App\Domain\Inventory\Repositories;

interface InventoryRepositoryInterface
{
    public function create(array $data): array;

    public function update(string $id, array $data): array;

    public function delete(string $id): bool;

    public function findById(string $id): ?array;

    public function exists(string $id): bool;

    public function existsForWarehouseAndVariant(string $warehouseId, string $productVariantId): bool;

    public function getByWarehouse(string $warehouseId): array;

    public function getByProductVariant(string $productVariantId): array;

    public function getAll(): array;

    public function getLowStock(int $threshold): array;

    /**
     * Check if enough stock is available for a variant in a specific warehouse.
     * Must use pessimistic lock to prevent race conditions.
     */
    public function hasAvailableStock(string $warehouseId, string $productVariantId, int $quantity): bool;

    public function findAvailableWarehouseForVariant(string $productVariantId, int $quantity): ?string;

    /**
     * Decrement quantity and create a reservation atomically.
     * Must be called inside a DB transaction with pessimistic lock.
     *
     * @return array The created reservation as array
     */
    public function reserveStock(string $warehouseId, string $productVariantId, int $quantity, string $orderId): array;

    /**
     * Release all reservations for a given order and restore inventory quantities.
     */
    public function releaseReservations(string $orderId): void;
}
