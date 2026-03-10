<?php

namespace App\Domain\Cart\Actions;

use App\Domain\Cart\Exceptions\CartItemNotFoundException;
use App\Domain\Cart\Repositories\CartRepositoryInterface;

final readonly class RemoveCartItemAction
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    ) {}

    /**
     * @throws CartItemNotFoundException
     */
    public function execute(int $userId, int $cartItemId): bool
    {
        // Validate cart item exists for user
        if (! $this->cartRepository->itemExists($userId, $cartItemId)) {
            throw CartItemNotFoundException::forUser($userId, $cartItemId);
        }

        return $this->cartRepository->removeItem($userId, $cartItemId);
    }
}
