<?php

namespace App\App\ProductVariant\Controllers;

use App\Domain\ProductVariant\Actions\CheckVariantInventoryAction;
use App\Domain\ProductVariant\Actions\GetProductVariantsAction;
use App\Domain\ProductVariant\Exceptions\ProductNotFoundException;
use App\Domain\ProductVariant\Exceptions\ProductVariantNotFoundException;
use App\Shared\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

final readonly class ProductVariantController
{
    use ApiResponse;

    public function __construct(
        private GetProductVariantsAction $getProductVariantsAction,
        private CheckVariantInventoryAction $checkVariantInventoryAction,
    ) {}

    public function index(string $productIdentifier): JsonResponse
    {
        try {
            return $this->success($this->getProductVariantsAction->execute($productIdentifier));
        } catch (ProductNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        }
    }

    public function checkInventory(int $variantId): JsonResponse
    {
        try {
            return $this->success($this->checkVariantInventoryAction->execute($variantId));
        } catch (ProductVariantNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        }
    }
}
