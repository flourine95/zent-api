<?php

namespace App\Domain\Cart\Actions;

use App\Domain\Cart\Repositories\CartRepositoryInterface;

final readonly class ClearCartAction
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    ) {}

    public function execute(string $userId): bool
    {
        return $this->cartRepository->clearCart($userId);
    }
}
