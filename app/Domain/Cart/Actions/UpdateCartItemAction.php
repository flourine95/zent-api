<?php

namespace App\Domain\Cart\Actions;

use App\Domain\Cart\DataTransferObjects\UpdateCartItemData;
use App\Domain\Cart\Exceptions\CartItemNotFoundException;
use App\Domain\Cart\Exceptions\InvalidQuantityException;
use App\Domain\Cart\Repositories\CartRepositoryInterface;

final readonly class UpdateCartItemAction
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    ) {}

    /**
     * @throws InvalidQuantityException
     * @throws CartItemNotFoundException
     */
    public function execute(UpdateCartItemData $data): array
    {
        // Validate quantity
        if ($data->quantity <= 0) {
            throw InvalidQuantityException::withValue($data->quantity);
        }

        // Validate cart item exists for user
        if (! $this->cartRepository->itemExists($data->userId, $data->cartItemId)) {
            throw CartItemNotFoundException::forUser($data->userId, $data->cartItemId);
        }

        return $this->cartRepository->updateItem(
            $data->userId,
            $data->cartItemId,
            $data->quantity
        );
    }
}
