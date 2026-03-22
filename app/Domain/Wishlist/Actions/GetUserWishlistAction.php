<?php

namespace App\Domain\Wishlist\Actions;

use App\Domain\Wishlist\Repositories\WishlistRepositoryInterface;

final readonly class GetUserWishlistAction
{
    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository
    ) {}

    public function execute(string $userId): array
    {
        return $this->wishlistRepository->getAllByUserId($userId);
    }
}
