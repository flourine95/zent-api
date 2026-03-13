<?php

namespace App\App\Cart\Controllers;

use App\App\Cart\Requests\AddCartItemRequest;
use App\App\Cart\Requests\UpdateCartItemRequest;
use App\Domain\Cart\Actions\AddCartItemAction;
use App\Domain\Cart\Actions\ClearCartAction;
use App\Domain\Cart\Actions\GetCartAction;
use App\Domain\Cart\Actions\RemoveCartItemAction;
use App\Domain\Cart\Actions\UpdateCartItemAction;
use App\Domain\Cart\DataTransferObjects\AddCartItemData;
use App\Domain\Cart\DataTransferObjects\UpdateCartItemData;
use App\Domain\Cart\Exceptions\CartItemNotFoundException;
use App\Domain\Cart\Exceptions\InvalidQuantityException;
use App\Domain\Cart\Exceptions\ProductVariantNotFoundException;
use App\Http\Resources\Api\CartResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class CartController
{
    public function __construct(
        private GetCartAction $getCartAction,
        private AddCartItemAction $addCartItemAction,
        private UpdateCartItemAction $updateCartItemAction,
        private RemoveCartItemAction $removeCartItemAction,
        private ClearCartAction $clearCartAction,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $cart = $this->getCartAction->execute($request->user()->id);

        return response()->json([
            'success' => true,
            'data' => new CartResource((object) $cart),
        ]);
    }

    public function addItem(AddCartItemRequest $request): JsonResponse
    {
        try {
            $data = AddCartItemData::fromArray([
                'user_id' => $request->user()->id,
                'product_variant_id' => $request->input('product_variant_id'),
                'quantity' => $request->input('quantity'),
            ]);

            $cart = $this->addCartItemAction->execute($data);

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng',
                'data' => new CartResource((object) $cart),
            ]);
        } catch (InvalidQuantityException|ProductVariantNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function updateItem(UpdateCartItemRequest $request, int $itemId): JsonResponse
    {
        try {
            $data = UpdateCartItemData::fromArray([
                'user_id' => $request->user()->id,
                'cart_item_id' => $itemId,
                'quantity' => $request->input('quantity'),
            ]);

            $cart = $this->updateCartItemAction->execute($data);

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật giỏ hàng',
                'data' => new CartResource((object) $cart),
            ]);
        } catch (InvalidQuantityException|CartItemNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function removeItem(Request $request, int $itemId): JsonResponse
    {
        try {
            $this->removeCartItemAction->execute($request->user()->id, $itemId);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
            ]);
        } catch (CartItemNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function clear(Request $request): JsonResponse
    {
        $this->clearCartAction->execute($request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa toàn bộ giỏ hàng',
        ]);
    }
}
