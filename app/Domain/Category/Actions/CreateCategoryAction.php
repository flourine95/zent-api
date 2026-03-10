<?php

namespace App\Domain\Category\Actions;

use App\Domain\Category\DataTransferObjects\CreateCategoryData;
use App\Domain\Category\Exceptions\CategoryNotFoundException;
use App\Domain\Category\Exceptions\InvalidCategoryHierarchyException;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;

final readonly class CreateCategoryAction
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    /**
     * @throws CategoryNotFoundException
     * @throws InvalidCategoryHierarchyException
     */
    public function execute(CreateCategoryData $data): array
    {
        // Validate parent exists if provided
        if ($data->parentId !== null) {
            if (! $this->categoryRepository->exists($data->parentId)) {
                throw CategoryNotFoundException::withId($data->parentId);
            }
        }

        return $this->categoryRepository->create($data->toArray());
    }
}
