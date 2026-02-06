<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Lấy danh sách sản phẩm
     */
    public function index(Request $request): JsonResponse
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'slug' => $product->slug,
                    'thumbnail' => $product->thumbnail ? asset('storage/'.$product->thumbnail) : null,
                    'specs' => $product->specs,
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name,
                    ] : null,
                    'created_at' => $product->created_at,
                ];
            }),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * Lấy chi tiết sản phẩm theo slug hoặc ID
     */
    public function show(Request $request, string $identifier): JsonResponse
    {
        // Tìm theo slug hoặc ID
        $product = is_numeric($identifier)
            ? Product::find($identifier)
            : Product::where('slug', $identifier)->first();

        if (! $product || ! $product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $product->load(['category', 'variants']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'slug' => $product->slug,
                'thumbnail' => $product->thumbnail ? asset('storage/'.$product->thumbnail) : null,
                'specs' => $product->specs,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                    'slug' => $product->category->slug,
                ] : null,
                'variants' => $product->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'sku' => $variant->sku,
                        'price' => $variant->price,
                        'options' => $variant->options,
                    ];
                }),
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ],
        ]);
    }
}
