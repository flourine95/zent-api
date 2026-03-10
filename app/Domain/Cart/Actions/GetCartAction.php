<?php

namespace App\Domain\Cart\Actions;

use App\Domain\Cart\Repositories\CartRepositoryInterface;

final readonly class GetCartAction
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    ) {}

    public function execute(int $userId): array
    {
        return $this->cartRepository->getByUserIdWithItems($userId);
    }
}
