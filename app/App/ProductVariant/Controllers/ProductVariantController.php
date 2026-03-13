<?php

namespace App\App\ProductVariant\Controllers;

use App\Domain\ProductVariant\Actions\CheckVariantInventoryAction;
use App\Domain\ProductVariant\Actions\GetProductVariantsAction;
use App\Domain\ProductVariant\Exceptions\ProductNotFoundException;
use App\Domain\ProductVariant\Exceptions\ProductVariantNotFoundException;
use App\Http\Resources\Api\ProductVariantResource;
use Illuminate\Http\JsonResponse;

final readonly class ProductVariantController
{
    public function __construct(
        private GetProductVariantsAction $getProductVariantsAction,
        private CheckVariantInventoryAction $checkVariantInventoryAction,
    ) {}

    public function index(string $productIdentifier): JsonResponse
    {
        try {
            $variants = $this->getProductVariantsAction->execute($productIdentifier);

            return response()->json([
                'success' => true,
                'data' => ProductVariantResource::collection(collect($variants)),
            ]);
        } catch (ProductNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm',
            ], 404);
        }
    }

    public function checkInventory(int $variantId): JsonResponse
    {
        try {
            $inventoryDetails = $this->checkVariantInventoryAction->execute($variantId);

            return response()->json([
                'success' => true,
                'data' => $inventoryDetails,
            ]);
        } catch (ProductVariantNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy biến thể sản phẩm',
            ], 404);
        }
    }
}
