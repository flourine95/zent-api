<?php

namespace App\Domain\ProductVariant\Actions;

use App\Domain\ProductVariant\Exceptions\ProductVariantNotFoundException;
use App\Domain\ProductVariant\Repositories\ProductVariantRepositoryInterface;

final readonly class CheckVariantInventoryAction
{
    public function __construct(
        private ProductVariantRepositoryInterface $productVariantRepository
    ) {}

    /**
     * @throws ProductVariantNotFoundException
     */
    public function execute(int $variantId): array
    {
        if (! $this->productVariantRepository->variantExists($variantId)) {
            throw ProductVariantNotFoundException::withId($variantId);
        }

        $inventoryDetails = $this->productVariantRepository->getInventoryDetails($variantId);

        if ($inventoryDetails === null) {
            throw ProductVariantNotFoundException::withId($variantId);
        }

        return $inventoryDetails;
    }
}
