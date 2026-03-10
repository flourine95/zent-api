<?php

namespace App\Domain\Wishlist\Actions;

use App\Domain\Wishlist\DataTransferObjects\AddWishlistData;
use App\Domain\Wishlist\Exceptions\ProductNotFoundException;
use App\Domain\Wishlist\Repositories\WishlistRepositoryInterface;

final readonly class AddToWishlistAction
{
    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository
    ) {}

    /**
     * @throws ProductNotFoundException
     */
    public function execute(AddWishlistData $data): array
    {
        // Validate product exists
        if (! $this->wishlistRepository->productExists($data->productId)) {
            throw ProductNotFoundException::withId($data->productId);
        }

        return $this->wishlistRepository->addProduct($data->userId, $data->productId);
    }
}
