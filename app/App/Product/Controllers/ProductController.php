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
use App\Shared\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

final readonly class ProductController
{
    use ApiResponse;

    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CreateProductAction $createProductAction,
        private UpdateProductAction $updateProductAction,
        private DeleteProductAction $deleteProductAction,
    ) {}

    public function index(): JsonResponse
    {
        return $this->success($this->productRepository->getAll());
    }

    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productRepository->findById($id);

            if ($product === null) {
                throw ProductNotFoundException::withId($id);
            }

            return $this->success($product);
        } catch (ProductNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        }
    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        try {
            $data = CreateProductData::fromArray($request->validated());

            return $this->created($this->createProductAction->execute($data));
        } catch (CategoryNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 422);
        }
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $data = UpdateProductData::fromArray($id, $request->validated());

            return $this->success($this->updateProductAction->execute($data));
        } catch (ProductNotFoundException|CategoryNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 422);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->deleteProductAction->execute($id);

            return $this->message('Product deleted successfully');
        } catch (ProductNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        }
    }
}
