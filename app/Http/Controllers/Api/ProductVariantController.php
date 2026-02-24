<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductVariantResource;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;

class ProductVariantController extends Controller
{
    /**
     * Lấy danh sách variants của sản phẩm
     */
    public function index(string $productIdentifier): JsonResponse
    {
        $product = is_numeric($productIdentifier)
            ? Product::find($productIdentifier)
            : Product::where('slug', $productIdentifier)->first();

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm',
            ], 404);
        }

        $variants = $product->variants()->get();

        return response()->json([
            'success' => true,
            'data' => ProductVariantResource::collection($variants),
        ]);
    }

    /**
     * Kiểm tra tồn kho của variant
     */
    public function checkInventory(int $variantId): JsonResponse
    {
        $variant = ProductVariant::with(['inventories.warehouse'])->find($variantId);

        if (! $variant) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy biến thể sản phẩm',
            ], 404);
        }

        $totalStock = $variant->inventories->sum('quantity');
        $inventoryByWarehouse = $variant->inventories->map(function ($inventory) {
            return [
                'warehouse_id' => $inventory->warehouse_id,
                'warehouse_name' => $inventory->warehouse->name ?? null,
                'quantity' => $inventory->quantity,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'variant_id' => $variant->id,
                'sku' => $variant->sku,
                'total_stock' => $totalStock,
                'in_stock' => $totalStock > 0,
                'warehouses' => $inventoryByWarehouse,
            ],
        ]);
    }
}
