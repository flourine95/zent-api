<?php

namespace App\Domain\Category\Actions;

use App\Domain\Category\DataTransferObjects\UpdateCategoryData;
use App\Domain\Category\Exceptions\CategoryNotFoundException;
use App\Domain\Category\Exceptions\InvalidCategoryHierarchyException;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;

final readonly class UpdateCategoryAction
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    /**
     * @throws CategoryNotFoundException
     * @throws InvalidCategoryHierarchyException
     */
    public function execute(UpdateCategoryData $data): array
    {
        // Validate category exists
        if (! $this->categoryRepository->exists($data->id)) {
            throw CategoryNotFoundException::withId($data->id);
        }

        // Validate parent hierarchy
        if ($data->parentId !== null) {
            // Cannot be its own parent
            if ($data->parentId === $data->id) {
                throw InvalidCategoryHierarchyException::selfReference($data->id);
            }

            // Parent must exist
            if (! $this->categoryRepository->exists($data->parentId)) {
                throw CategoryNotFoundException::withId($data->parentId);
            }

            // Cannot create circular reference (parent cannot be a descendant)
            if ($this->categoryRepository->isDescendantOf($data->parentId, $data->id)) {
                throw InvalidCategoryHierarchyException::circularReference($data->id, $data->parentId);
            }
        }

        return $this->categoryRepository->update($data->id, $data->toArray());
    }
}
