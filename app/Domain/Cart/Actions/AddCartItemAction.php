<?php

namespace App\Domain\Cart\Actions;

use App\Domain\Cart\DataTransferObjects\AddCartItemData;
use App\Domain\Cart\Exceptions\InvalidQuantityException;
use App\Domain\Cart\Exceptions\ProductVariantNotFoundException;
use App\Domain\Cart\Repositories\CartRepositoryInterface;

final readonly class AddCartItemAction
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    ) {}

    /**
     * @throws InvalidQuantityException
     * @throws ProductVariantNotFoundException
     */
    public function execute(AddCartItemData $data): array
    {
        // Validate quantity
        if ($data->quantity <= 0) {
            throw InvalidQuantityException::withValue($data->quantity);
        }

        // Validate product variant exists
        if (! $this->cartRepository->variantExists($data->productVariantId)) {
            throw ProductVariantNotFoundException::withId($data->productVariantId);
        }

        return $this->cartRepository->addItem(
            $data->userId,
            $data->productVariantId,
            $data->quantity
        );
    }
}
