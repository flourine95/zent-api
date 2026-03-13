<?php

namespace App\App\Product\Controllers;

use App\App\Product\Requests\CreateProductRequest;
use App\App\Product\Requests\UpdateProductRequest;
use App\Domain\Category\Exceptions\CategoryNotFoundException;
use App\Domain\Product\Actions\CreateProductAction;
use App\Domain\Product\Actions\DeleteProductAction;
use App\Domain\Product\Actions\UpdateProductAction;
use App\Domain\Product\DataTransferObjects\CreateProductData;
use App\Domain\Product\DataTransferObjects\UpdateProductData;
use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;

final readonly class ProductController
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CreateProductAction $createProductAction,
        private UpdateProductAction $updateProductAction,
        private DeleteProductAction $deleteProductAction,
    ) {}

    public function index(): JsonResponse
    {
        $products = $this->productRepository->getAll();

        return response()->json(['data' => $products]);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productRepository->findById($id);

            if ($product === null) {
                throw ProductNotFoundException::withId($id);
            }

            return response()->json(['data' => $product]);
        } catch (ProductNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        try {
            $data = CreateProductData::fromArray($request->validated());
            $product = $this->createProductAction->execute($data);

            return response()->json(['data' => $product], 201);
        } catch (CategoryNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $data = UpdateProductData::fromArray($id, $request->validated());
            $product = $this->updateProductAction->execute($data);

            return response()->json(['data' => $product]);
        } catch (ProductNotFoundException|CategoryNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->deleteProductAction->execute($id);

            return response()->json(['message' => 'Product deleted successfully']);
        } catch (ProductNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
