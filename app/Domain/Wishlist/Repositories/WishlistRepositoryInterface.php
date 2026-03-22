<?php

namespace App\Domain\Wishlist\Repositories;

interface WishlistRepositoryInterface
{
    public function getAllByUserId(string $userId): array;

    public function addProduct(string $userId, string $productId): array;

    public function removeProduct(string $userId, string $productId): bool;

    public function isProductInWishlist(string $userId, string $productId): bool;

    public function productExists(string $productId): bool;

    public function findByUserAndProduct(string $userId, string $productId): ?array;
}
