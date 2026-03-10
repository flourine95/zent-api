<?php

namespace App\Domain\Wishlist\Repositories;

interface WishlistRepositoryInterface
{
    public function getAllByUserId(int $userId): array;

    public function addProduct(int $userId, int $productId): array;

    public function removeProduct(int $userId, int $productId): bool;

    public function isProductInWishlist(int $userId, int $productId): bool;

    public function productExists(int $productId): bool;

    public function findByUserAndProduct(int $userId, int $productId): ?array;
}
