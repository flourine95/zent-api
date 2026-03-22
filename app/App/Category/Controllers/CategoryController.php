<?php

namespace App\App\Category\Controllers;

use App\App\Category\Requests\CreateCategoryRequest;
use App\App\Category\Requests\UpdateCategoryRequest;
use App\Domain\Category\Actions\CreateCategoryAction;
use App\Domain\Category\Actions\DeleteCategoryAction;
use App\Domain\Category\Actions\UpdateCategoryAction;
use App\Domain\Category\DataTransferObjects\CreateCategoryData;
use App\Domain\Category\DataTransferObjects\UpdateCategoryData;
use App\Domain\Category\Exceptions\CategoryNotFoundException;
use App\Domain\Category\Exceptions\InvalidCategoryHierarchyException;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Shared\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

final readonly class CategoryController
{
    use ApiResponse;

    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private CreateCategoryAction $createCategoryAction,
        private UpdateCategoryAction $updateCategoryAction,
        private DeleteCategoryAction $deleteCategoryAction,
    ) {}

    public function index(): JsonResponse
    {
        return $this->success($this->categoryRepository->getAll());
    }

    public function tree(): JsonResponse
    {
        return $this->success($this->categoryRepository->getTree());
    }

    public function show(string $id): JsonResponse
    {
        try {
            $category = $this->categoryRepository->findById($id);

            if ($category === null) {
                throw CategoryNotFoundException::withId($id);
            }

            return $this->success($category);
        } catch (CategoryNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        }
    }

    public function store(CreateCategoryRequest $request): JsonResponse
    {
        try {
            $data = CreateCategoryData::fromArray($request->validated());

            return $this->created($this->createCategoryAction->execute($data));
        } catch (CategoryNotFoundException|InvalidCategoryHierarchyException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 422);
        }
    }

    public function update(UpdateCategoryRequest $request, string $id): JsonResponse
    {
        try {
            $data = UpdateCategoryData::fromArray($id, $request->validated());

            return $this->success($this->updateCategoryAction->execute($data));
        } catch (CategoryNotFoundException|InvalidCategoryHierarchyException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 422);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->deleteCategoryAction->execute($id);

            return $this->message('Category deleted successfully');
        } catch (CategoryNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        }
    }
}
