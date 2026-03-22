<?php

namespace App\Infrastructure\Repositories;

use App\Domain\ProductVariant\Repositories\ProductVariantRepositoryInterface;
use App\Infrastructure\Models\Product;
use App\Infrastructure\Models\ProductVariant;

final class EloquentProductVariantRepository implements ProductVariantRepositoryInterface
{
    public function getByProductId(string $productId): array
    {
        return ProductVariant::where('product_id', $productId)
            ->get()
            ->toArray();
    }

    public function getByProductSlug(string $slug): array
    {
        $product = Product::where('slug', $slug)->first();

        if (! $product) {
            return [];
        }

        return $product->variants()->get()->toArray();
    }

    public function findById(string $id): ?array
    {
        $variant = ProductVariant::find($id);

        return $variant?->toArray();
    }

    public function getInventoryDetails(string $variantId): ?array
    {
        $variant = ProductVariant::with(['inventories.warehouse'])->find($variantId);

        if (! $variant) {
            return null;
        }

        $totalStock = $variant->inventories->sum('quantity');
        $inventoryByWarehouse = $variant->inventories->map(function ($inventory) {
            return [
                'warehouse_id' => $inventory->warehouse_id,
                'warehouse_name' => $inventory->warehouse->name ?? null,
                'quantity' => $inventory->quantity,
            ];
        })->toArray();

        return [
            'variant_id' => $variant->id,
            'sku' => $variant->sku,
            'total_stock' => $totalStock,
            'in_stock' => $totalStock > 0,
            'warehouses' => $inventoryByWarehouse,
        ];
    }

    public function productExists(string $productId): bool
    {
        return Product::where('id', $productId)->exists();
    }

    public function productExistsBySlug(string $slug): bool
    {
        return Product::where('slug', $slug)->exists();
    }

    public function variantExists(string $variantId): bool
    {
        return ProductVariant::where('id', $variantId)->exists();
    }
}
