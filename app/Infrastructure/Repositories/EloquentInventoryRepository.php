<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Inventory\Repositories\InventoryRepositoryInterface;
use App\Infrastructure\Models\Inventory;
use App\Infrastructure\Models\InventoryReservation;
use Illuminate\Support\Facades\DB;

final class EloquentInventoryRepository implements InventoryRepositoryInterface
{
    public function create(array $data): array
    {
        $inventory = Inventory::create($data);

        return $inventory->load(['warehouse', 'productVariant'])->toArray();
    }

    public function update(string $id, array $data): array
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->update($data);

        return $inventory->fresh(['warehouse', 'productVariant'])->toArray();
    }

    public function delete(string $id): bool
    {
        $inventory = Inventory::findOrFail($id);

        return $inventory->delete();
    }

    public function findById(string $id): ?array
    {
        $inventory = Inventory::with(['warehouse', 'productVariant'])->find($id);

        return $inventory?->toArray();
    }

    public function exists(string $id): bool
    {
        return Inventory::where('id', $id)->exists();
    }

    public function existsForWarehouseAndVariant(string $warehouseId, string $productVariantId): bool
    {
        return Inventory::where('warehouse_id', $warehouseId)
            ->where('product_variant_id', $productVariantId)
            ->exists();
    }

    public function getByWarehouse(string $warehouseId): array
    {
        return Inventory::with(['productVariant'])
            ->where('warehouse_id', $warehouseId)
            ->orderBy('shelf_location')
            ->get()
            ->toArray();
    }

    public function getByProductVariant(string $productVariantId): array
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

    public function hasAvailableStock(string $warehouseId, string $productVariantId, int $quantity): bool
    {
        $inventory = Inventory::where('warehouse_id', $warehouseId)
            ->where('product_variant_id', $productVariantId)
            ->first();

        return $inventory !== null && $inventory->quantity >= $quantity;
    }

    public function findAvailableWarehouseForVariant(string $productVariantId, int $quantity): ?string
    {
        $inventory = Inventory::where('product_variant_id', $productVariantId)
            ->where('quantity', '>=', $quantity)
            ->first();

        return $inventory?->warehouse_id;
    }

    public function findAvailableWarehousesForVariants(array $variantQuantities): array
    {
        if (empty($variantQuantities)) {
            return [];
        }

        $variantIds = array_keys($variantQuantities);

        $rows = Inventory::whereIn('product_variant_id', $variantIds)
            ->where('quantity', '>', 0)
            ->get(['product_variant_id', 'warehouse_id', 'quantity']);

        $result = [];

        foreach ($rows as $row) {
            $variantId = $row->product_variant_id;
            $required = $variantQuantities[$variantId] ?? 0;

            if (! isset($result[$variantId]) && $row->quantity >= $required) {
                $result[$variantId] = $row->warehouse_id;
            }
        }

        return $result;
    }

    public function reserveStock(string $warehouseId, string $productVariantId, int $quantity, string $orderId): array
    {
        $inventory = Inventory::where('warehouse_id', $warehouseId)
            ->where('product_variant_id', $productVariantId)
            ->lockForUpdate()
            ->firstOrFail();

        $inventory->decrement('quantity', $quantity);

        $reservation = InventoryReservation::create([
            'inventory_id' => $inventory->id,
            'order_id' => $orderId,
            'product_variant_id' => $productVariantId,
            'quantity' => $quantity,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(30),
        ]);

        return $reservation->toArray();
    }

    public function releaseReservations(string $orderId): void
    {
        $reservations = InventoryReservation::where('order_id', $orderId)
            ->where('status', 'pending')
            ->get();

        foreach ($reservations as $reservation) {
            DB::transaction(function () use ($reservation) {
                Inventory::where('id', $reservation->inventory_id)
                    ->lockForUpdate()
                    ->firstOrFail()
                    ->increment('quantity', $reservation->quantity);

                $reservation->update(['status' => 'released']);
            });
        }
    }
}
