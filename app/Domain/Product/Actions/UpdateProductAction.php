<?php

namespace App\Domain\Product\Actions;

use App\Domain\Category\Exceptions\CategoryNotFoundException;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Domain\Product\DataTransferObjects\UpdateProductData;
use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Product\Repositories\ProductRepositoryInterface;

final readonly class UpdateProductAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    /**
     * @throws ProductNotFoundException
     * @throws CategoryNotFoundException
     */
    public function execute(UpdateProductData $data): array
    {
        // Validate product exists
        if (! $this->productRepository->exists($data->id)) {
            throw ProductNotFoundException::withId($data->id);
        }

        // Validate category exists
        if (! $this->categoryRepository->exists($data->categoryId)) {
            throw CategoryNotFoundException::withId($data->categoryId);
        }

        return $this->productRepository->update($data->id, $data->toArray());
    }
}
