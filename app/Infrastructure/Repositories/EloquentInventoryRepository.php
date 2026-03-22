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

    public function hasAvailableStock(int $warehouseId, int $productVariantId, int $quantity): bool
    {
        $inventory = Inventory::where('warehouse_id', $warehouseId)
            ->where('product_variant_id', $productVariantId)
            ->lockForUpdate()
            ->first();

        return $inventory !== null && $inventory->quantity >= $quantity;
    }

    public function findAvailableWarehouseForVariant(int $productVariantId, int $quantity): ?int
    {
        $inventory = Inventory::where('product_variant_id', $productVariantId)
            ->where('quantity', '>=', $quantity)
            ->first();

        return $inventory?->warehouse_id;
    }

    public function reserveStock(int $warehouseId, int $productVariantId, int $quantity, int $orderId): array
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

    public function releaseReservations(int $orderId): void
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
