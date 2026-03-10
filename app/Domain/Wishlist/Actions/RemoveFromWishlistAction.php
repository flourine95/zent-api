<?php

namespace App\Domain\Wishlist\Actions;

use App\Domain\Wishlist\Exceptions\WishlistItemNotFoundException;
use App\Domain\Wishlist\Repositories\WishlistRepositoryInterface;

final readonly class RemoveFromWishlistAction
{
    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository
    ) {}

    /**
     * @throws WishlistItemNotFoundException
     */
    public function execute(int $userId, int $productId): bool
    {
        // Check if product is in wishlist
        if (! $this->wishlistRepository->isProductInWishlist($userId, $productId)) {
            throw WishlistItemNotFoundException::forProduct($productId);
        }

        return $this->wishlistRepository->removeProduct($userId, $productId);
    }
}
