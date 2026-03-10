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
use Illuminate\Http\JsonResponse;

final class CategoryController
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly CreateCategoryAction $createCategoryAction,
        private readonly UpdateCategoryAction $updateCategoryAction,
        private readonly DeleteCategoryAction $deleteCategoryAction,
    ) {}

    public function index(): JsonResponse
    {
        $categories = $this->categoryRepository->getAll();

        return response()->json(['data' => $categories]);
    }

    public function tree(): JsonResponse
    {
        $tree = $this->categoryRepository->getTree();

        return response()->json(['data' => $tree]);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $category = $this->categoryRepository->findById($id);

            if ($category === null) {
                throw CategoryNotFoundException::withId($id);
            }

            return response()->json(['data' => $category]);
        } catch (CategoryNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function store(CreateCategoryRequest $request): JsonResponse
    {
        try {
            $data = CreateCategoryData::fromArray($request->validated());
            $category = $this->createCategoryAction->execute($data);

            return response()->json(['data' => $category], 201);
        } catch (CategoryNotFoundException|InvalidCategoryHierarchyException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        try {
            $data = UpdateCategoryData::fromArray($id, $request->validated());
            $category = $this->updateCategoryAction->execute($data);

            return response()->json(['data' => $category]);
        } catch (CategoryNotFoundException|InvalidCategoryHierarchyException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->deleteCategoryAction->execute($id);

            return response()->json(['message' => 'Category deleted successfully']);
        } catch (CategoryNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
