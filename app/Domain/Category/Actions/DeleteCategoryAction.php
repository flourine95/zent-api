<?php

namespace App\Domain\Category\Actions;

use App\Domain\Category\Exceptions\CategoryNotFoundException;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;

final readonly class DeleteCategoryAction
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    /**
     * @throws CategoryNotFoundException
     */
    public function execute(int $categoryId): bool
    {
        if (! $this->categoryRepository->exists($categoryId)) {
            throw CategoryNotFoundException::withId($categoryId);
        }

        return $this->categoryRepository->delete($categoryId);
    }
}
