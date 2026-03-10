<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CartResource;
use App\Infrastructure\Models\ProductVariant;
use App\Models\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Lấy giỏ hàng của user
     */
    public function index(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request->user()->id);
        $cart->load(['items.productVariant.product']);

        return response()->json([
            'success' => true,
            'data' => new CartResource($cart),
        ]);
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->getOrCreateCart($request->user()->id);

        $variant = ProductVariant::findOrFail($request->product_variant_id);

        // Check if item already exists
        $cartItem = $cart->items()->where('product_variant_id', $variant->id)->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $request->quantity);
        } else {
            $cart->items()->create([
                'product_variant_id' => $variant->id,
                'quantity' => $request->quantity,
            ]);
        }

        $cart->load(['items.productVariant.product']);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm sản phẩm vào giỏ hàng',
            'data' => new CartResource($cart),
        ]);
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ
     */
    public function updateItem(Request $request, int $itemId): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->getOrCreateCart($request->user()->id);
        $cartItem = $cart->items()->findOrFail($itemId);

        $cartItem->update(['quantity' => $request->quantity]);

        $cart->load(['items.productVariant.product']);

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật giỏ hàng',
            'data' => new CartResource($cart),
        ]);
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function removeItem(Request $request, int $itemId): JsonResponse
    {
        $cart = $this->getOrCreateCart($request->user()->id);
        $cartItem = $cart->items()->findOrFail($itemId);

        $cartItem->delete();

        $cart->load(['items.productVariant.product']);

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
            'data' => new CartResource($cart),
        ]);
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request->user()->id);
        $cart->items()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa toàn bộ giỏ hàng',
        ]);
    }

    /**
     * Get or create cart for user
     */
    private function getOrCreateCart(int $userId): Cart
    {
        return Cart::firstOrCreate(['user_id' => $userId]);
    }
}
