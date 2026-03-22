<?php

namespace App\Domain\ProductVariant\Repositories;

interface ProductVariantRepositoryInterface
{
    public function getByProductId(string $productId): array;

    public function getByProductSlug(string $slug): array;

    public function findById(string $id): ?array;

    public function getInventoryDetails(string $variantId): ?array;

    public function productExists(string $productId): bool;

    public function productExistsBySlug(string $slug): bool;

    public function variantExists(string $variantId): bool;
}
