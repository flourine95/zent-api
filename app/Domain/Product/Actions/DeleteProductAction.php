<?php

namespace App\Domain\Product\Actions;

use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Product\Repositories\ProductRepositoryInterface;

final readonly class DeleteProductAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    /**
     * @throws ProductNotFoundException
     */
    public function execute(string $productId): bool
    {
        if (! $this->productRepository->exists($productId)) {
            throw ProductNotFoundException::withId($productId);
        }

        return $this->productRepository->delete($productId);
    }
}
