<?php

namespace App\Domain\Product\Actions;

use App\Domain\Category\Exceptions\CategoryNotFoundException;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Domain\Product\DataTransferObjects\CreateProductData;
use App\Domain\Product\Repositories\ProductRepositoryInterface;

final readonly class CreateProductAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    /**
     * @throws CategoryNotFoundException
     */
    public function execute(CreateProductData $data): array
    {
        // Validate category exists
        if (! $this->categoryRepository->exists($data->categoryId)) {
            throw CategoryNotFoundException::withId($data->categoryId);
        }

        return $this->productRepository->create($data->toArray());
    }
}
