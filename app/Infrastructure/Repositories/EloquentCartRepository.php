<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Cart\Repositories\CartRepositoryInterface;
use App\Infrastructure\Models\Cart;
use App\Infrastructure\Models\ProductVariant;

final class EloquentCartRepository implements CartRepositoryInterface
{
    public function getOrCreateByUserId(int $userId): array
    {
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        return $cart->toArray();
    }

    public function getByUserIdWithItems(int $userId): array
    {
        $cart = Cart::with(['items.productVariant.product'])
            ->firstOrCreate(['user_id' => $userId]);

        return $cart->toArray();
    }

    public function addItem(int $userId, int $productVariantId, int $quantity): array
    {
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        // Check if item already exists
        $cartItem = $cart->items()->where('product_variant_id', $productVariantId)->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'product_variant_id' => $productVariantId,
                'quantity' => $quantity,
            ]);
        }

        $cart->load(['items.productVariant.product']);

        return $cart->toArray();
    }

    public function updateItem(int $userId, int $cartItemId, int $quantity): array
    {
        $cart = Cart::where('user_id', $userId)->firstOrFail();
        $cartItem = $cart->items()->findOrFail($cartItemId);

        $cartItem->update(['quantity' => $quantity]);

        $cart->load(['items.productVariant.product']);

        return $cart->toArray();
    }

    public function removeItem(int $userId, int $cartItemId): bool
    {
        $cart = Cart::where('user_id', $userId)->firstOrFail();
        $cartItem = $cart->items()->findOrFail($cartItemId);

        return $cartItem->delete();
    }

    public function clearCart(int $userId): bool
    {
        $cart = Cart::where('user_id', $userId)->first();

        if ($cart) {
            $cart->items()->delete();

            return true;
        }

        return false;
    }

    public function itemExists(int $userId, int $cartItemId): bool
    {
        return Cart::where('user_id', $userId)
            ->whereHas('items', function ($query) use ($cartItemId) {
                $query->where('id', $cartItemId);
            })
            ->exists();
    }

    public function variantExists(int $productVariantId): bool
    {
        return ProductVariant::where('id', $productVariantId)->exists();
    }

    public function getItemByVariant(int $userId, int $productVariantId): ?array
    {
        $cart = Cart::where('user_id', $userId)->first();

        if (! $cart) {
            return null;
        }

        $item = $cart->items()->where('product_variant_id', $productVariantId)->first();

        return $item?->toArray();
    }
}
