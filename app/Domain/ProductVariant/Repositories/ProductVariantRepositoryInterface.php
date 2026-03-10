<?php

namespace App\Domain\ProductVariant\Repositories;

interface ProductVariantRepositoryInterface
{
    public function getByProductId(int $productId): array;

    public function getByProductSlug(string $slug): array;

    public function findById(int $id): ?array;

    public function getInventoryDetails(int $variantId): ?array;

    public function productExists(int $productId): bool;

    public function productExistsBySlug(string $slug): bool;

    public function variantExists(int $variantId): bool;
}
