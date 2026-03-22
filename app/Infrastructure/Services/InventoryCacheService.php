<?php

namespace App\Infrastructure\Services;

use App\Domain\Inventory\Exceptions\InsufficientStockException;
use App\Domain\Inventory\Services\InventoryCacheServiceInterface;
use App\Infrastructure\Models\Inventory;
use Illuminate\Support\Facades\Redis;

class InventoryCacheService implements InventoryCacheServiceInterface
{
    private const TTL = 3600; // 1 hour

    private const KEY_PREFIX = 'inventory:variant:';

    public function decrementBatch(array $variantQuantities): void
    {
        // Ensure all keys are warm before attempting decrements
        foreach (array_keys($variantQuantities) as $variantId) {
            $this->warmIfMissing($variantId);
        }

        $decremented = [];

        foreach ($variantQuantities as $variantId => $quantity) {
            $key = self::KEY_PREFIX.$variantId;
            $remaining = Redis::decrby($key, $quantity);

            if ($remaining < 0) {
                // Roll back all successful decrements before throwing
                Redis::incrby($key, $quantity);

                foreach ($decremented as $rolledVariantId => $rolledQty) {
                    Redis::incrby(self::KEY_PREFIX.$rolledVariantId, $rolledQty);
                }

                throw InsufficientStockException::forVariantId($variantId);
            }

            $decremented[$variantId] = $quantity;
        }
    }

    public function incrementBatch(array $variantQuantities): void
    {
        foreach ($variantQuantities as $variantId => $quantity) {
            Redis::incrby(self::KEY_PREFIX.$variantId, $quantity);
        }
    }

    public function getStock(string $variantId): int
    {
        $this->warmIfMissing($variantId);

        return (int) Redis::get(self::KEY_PREFIX.$variantId);
    }

    private function warmIfMissing(string $variantId): void
    {
        $key = self::KEY_PREFIX.$variantId;

        if (Redis::exists($key)) {
            return;
        }

        // Lazy load: sum available stock across all warehouses
        $stock = Inventory::where('product_variant_id', $variantId)->sum('quantity');

        Redis::setex($key, self::TTL, max(0, (int) $stock));
    }
}
