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
use App\Shared\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class WishlistController
{
    use ApiResponse;

    public function __construct(
        private GetUserWishlistAction $getUserWishlistAction,
        private AddToWishlistAction $addToWishlistAction,
        private RemoveFromWishlistAction $removeFromWishlistAction,
        private CheckWishlistAction $checkWishlistAction,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return $this->success($this->getUserWishlistAction->execute($request->user()->id));
    }

    public function store(AddWishlistRequest $request): JsonResponse
    {
        try {
            $data = AddWishlistData::fromArray([
                'user_id' => $request->user()->id,
                'product_id' => $request->input('product_id'),
            ]);

            return $this->created($this->addToWishlistAction->execute($data), 'Added to wishlist');
        } catch (ProductNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        }
    }

    public function destroy(Request $request, string $productId): JsonResponse
    {
        try {
            $this->removeFromWishlistAction->execute($request->user()->id, $productId);

            return $this->message('Removed from wishlist');
        } catch (WishlistItemNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        }
    }

    public function check(Request $request, string $productId): JsonResponse
    {
        return $this->success([
            'in_wishlist' => $this->checkWishlistAction->execute($request->user()->id, $productId),
        ]);
    }
}
