<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Wishlist\Repositories\WishlistRepositoryInterface;
use App\Infrastructure\Models\Product;
use App\Infrastructure\Models\Wishlist;

final class EloquentWishlistRepository implements WishlistRepositoryInterface
{
    public function getAllByUserId(string $userId): array
    {
        return Wishlist::with(['product.category', 'product.variants'])
            ->where('user_id', $userId)
            ->latest()
            ->get()
            ->toArray();
    }

    public function addProduct(string $userId, string $productId): array
    {
        $wishlist = Wishlist::firstOrCreate([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        $wishlist->load(['product.category', 'product.variants']);

        return $wishlist->toArray();
    }

    public function removeProduct(string $userId, string $productId): bool
    {
        return Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete() > 0;
    }

    public function isProductInWishlist(string $userId, string $productId): bool
    {
        return Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }

    public function productExists(string $productId): bool
    {
        return Product::where('id', $productId)->exists();
    }

    public function findByUserAndProduct(string $userId, string $productId): ?array
    {
        $wishlist = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        return $wishlist?->toArray();
    }
}
