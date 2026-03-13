<?php

namespace App\App\Wishlist\Controllers;

use App\App\Wishlist\Requests\AddWishlistRequest;
use App\Domain\Wishlist\Actions\AddToWishlistAction;
use App\Domain\Wishlist\Actions\CheckWishlistAction;
use App\Domain\Wishlist\Actions\GetUserWishlistAction;
use App\Domain\Wishlist\Actions\RemoveFromWishlistAction;
use App\Domain\Wishlist\DataTransferObjects\AddWishlistData;
use App\Domain\Wishlist\Exceptions\ProductNotFoundException;
use App\Domain\Wishlist\Exceptions\WishlistItemNotFoundException;
use App\Http\Resources\Api\WishlistResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class WishlistController
{
    public function __construct(
        private GetUserWishlistAction $getUserWishlistAction,
        private AddToWishlistAction $addToWishlistAction,
        private RemoveFromWishlistAction $removeFromWishlistAction,
        private CheckWishlistAction $checkWishlistAction,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $wishlists = $this->getUserWishlistAction->execute($request->user()->id);

        return response()->json([
            'success' => true,
            'data' => WishlistResource::collection(collect($wishlists)),
        ]);
    }

    public function store(AddWishlistRequest $request): JsonResponse
    {
        try {
            $data = AddWishlistData::fromArray([
                'user_id' => $request->user()->id,
                'product_id' => $request->input('product_id'),
            ]);

            $wishlist = $this->addToWishlistAction->execute($data);

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm vào danh sách yêu thích',
                'data' => new WishlistResource((object) $wishlist),
            ], 201);
        } catch (ProductNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function destroy(Request $request, int $productId): JsonResponse
    {
        try {
            $this->removeFromWishlistAction->execute($request->user()->id, $productId);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa khỏi danh sách yêu thích',
            ]);
        } catch (WishlistItemNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong danh sách yêu thích',
            ], 404);
        }
    }

    public function check(Request $request, int $productId): JsonResponse
    {
        $inWishlist = $this->checkWishlistAction->execute($request->user()->id, $productId);

        return response()->json([
            'success' => true,
            'in_wishlist' => $inWishlist,
        ]);
    }
}
