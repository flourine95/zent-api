<?php

namespace App\Domain\ProductVariant\Actions;

use App\Domain\ProductVariant\Exceptions\ProductNotFoundException;
use App\Domain\ProductVariant\Repositories\ProductVariantRepositoryInterface;

final readonly class GetProductVariantsAction
{
    public function __construct(
        private ProductVariantRepositoryInterface $productVariantRepository
    ) {}

    /**
     * @throws ProductNotFoundException
     */
    public function execute(string $productIdentifier): array
    {
        // Check if identifier is numeric (ID) or string (slug)
        if (is_numeric($productIdentifier)) {
            $productId = (int) $productIdentifier;

            if (! $this->productVariantRepository->productExists($productId)) {
                throw ProductNotFoundException::withIdentifier($productIdentifier);
            }

            return $this->productVariantRepository->getByProductId($productId);
        }

        // It's a slug
        if (! $this->productVariantRepository->productExistsBySlug($productIdentifier)) {
            throw ProductNotFoundException::withIdentifier($productIdentifier);
        }

        return $this->productVariantRepository->getByProductSlug($productIdentifier);
    }
}
