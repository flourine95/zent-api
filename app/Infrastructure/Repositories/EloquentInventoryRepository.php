<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Inventory\Repositories\InventoryRepositoryInterface;
use App\Infrastructure\Models\Inventory;

final class EloquentInventoryRepository implements InventoryRepositoryInterface
{
    public function create(array $data): array
    {
        $inventory = Inventory::create($data);

        return $inventory->load(['warehouse', 'productVariant'])->toArray();
    }

    public function update(int $id, array $data): array
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->update($data);

        return $inventory->fresh(['warehouse', 'productVariant'])->toArray();
    }

    public function delete(int $id): bool
    {
        $inventory = Inventory::findOrFail($id);

        return $inventory->delete();
    }

    public function findById(int $id): ?array
    {
        $inventory = Inventory::with(['warehouse', 'productVariant'])->find($id);

        return $inventory?->toArray();
    }

    public function exists(int $id): bool
    {
        return Inventory::where('id', $id)->exists();
    }

    public function existsForWarehouseAndVariant(int $warehouseId, int $productVariantId): bool
    {
        return Inventory::where('warehouse_id', $warehouseId)
            ->where('product_variant_id', $productVariantId)
            ->exists();
    }

    public function getByWarehouse(int $warehouseId): array
    {
        return Inventory::with(['productVariant'])
            ->where('warehouse_id', $warehouseId)
            ->orderBy('shelf_location')
            ->get()
            ->toArray();
    }

    public function getByProductVariant(int $productVariantId): array
    {
        return Inventory::with(['warehouse'])
            ->where('product_variant_id', $productVariantId)
            ->get()
            ->toArray();
    }

    public function getAll(): array
    {
        return Inventory::with(['warehouse', 'productVariant'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function getLowStock(int $threshold): array
    {
        return Inventory::with(['warehouse', 'productVariant'])
            ->where('quantity', '<=', $threshold)
            ->orderBy('quantity')
            ->get()
            ->toArray();
    }
}
