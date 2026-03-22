<?php

namespace App\Domain\Inventory\Services;

interface InventoryCacheServiceInterface
{
    /**
     * Atomically decrement stock for multiple variants.
     * Lazy-loads from DB on cache miss.
     * Rolls back all decrements if any variant is insufficient.
     *
     * @param  array<string, int>  $variantQuantities  Map of variant_id => quantity
     *
     * @throws \App\Domain\Inventory\Exceptions\InsufficientStockException
     */
    public function decrementBatch(array $variantQuantities): void;

    /**
     * Restore stock for multiple variants (compensation on job failure).
     *
     * @param  array<string, int>  $variantQuantities  Map of variant_id => quantity
     */
    public function incrementBatch(array $variantQuantities): void;

    /**
     * Get available stock for a variant (lazy-loads from DB on miss).
     */
    public function getStock(string $variantId): int;
}
