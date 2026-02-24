<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\WishlistResource;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Lấy danh sách yêu thích
     */
    public function index(Request $request): JsonResponse
    {
        $wishlists = $request->user()
            ->wishlists()
            ->with(['product.category', 'product.variants'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => WishlistResource::collection($wishlists),
        ]);
    }

    /**
     * Thêm sản phẩm vào danh sách yêu thích
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->product_id);

        $wishlist = $request->user()->wishlists()->firstOrCreate([
            'product_id' => $product->id,
        ]);

        $wishlist->load(['product.category', 'product.variants']);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào danh sách yêu thích',
            'data' => new WishlistResource($wishlist),
        ], 201);
    }

    /**
     * Xóa sản phẩm khỏi danh sách yêu thích
     */
    public function destroy(Request $request, int $productId): JsonResponse
    {
        $wishlist = $request->user()
            ->wishlists()
            ->where('product_id', $productId)
            ->first();

        if (! $wishlist) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong danh sách yêu thích',
            ], 404);
        }

        $wishlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa khỏi danh sách yêu thích',
        ]);
    }

    /**
     * Kiểm tra sản phẩm có trong wishlist không
     */
    public function check(Request $request, int $productId): JsonResponse
    {
        $exists = $request->user()
            ->wishlists()
            ->where('product_id', $productId)
            ->exists();

        return response()->json([
            'success' => true,
            'in_wishlist' => $exists,
        ]);
    }
}
