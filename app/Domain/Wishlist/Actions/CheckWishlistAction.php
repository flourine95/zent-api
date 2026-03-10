<?php

namespace App\Domain\Wishlist\Actions;

use App\Domain\Wishlist\Repositories\WishlistRepositoryInterface;

final readonly class CheckWishlistAction
{
    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository
    ) {}

    public function execute(int $userId, int $productId): bool
    {
        return $this->wishlistRepository->isProductInWishlist($userId, $productId);
    }
}
