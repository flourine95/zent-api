<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Cart\Repositories\CartRepositoryInterface;
use App\Infrastructure\Models\Cart;
use App\Infrastructure\Models\ProductVariant;

final class EloquentCartRepository implements CartRepositoryInterface
{
    public function getOrCreateByUserId(string $userId): array
    {
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        return $cart->toArray();
    }

    public function getByUserIdWithItems(string $userId): array
    {
        $cart = Cart::with(['items.productVariant.product'])
            ->firstOrCreate(['user_id' => $userId]);

        return $this->formatCart($cart);
    }

    public function addItem(string $userId, string $productVariantId, int $quantity): array
    {
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

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

        return $this->formatCart($cart);
    }

    public function updateItem(string $userId, string $cartItemId, int $quantity): array
    {
        $cart = Cart::where('user_id', $userId)->firstOrFail();
        $cartItem = $cart->items()->findOrFail($cartItemId);

        $cartItem->update(['quantity' => $quantity]);

        $cart->load(['items.productVariant.product']);

        return $this->formatCart($cart);
    }

    public function removeItem(string $userId, string $cartItemId): bool
    {
        $cart = Cart::where('user_id', $userId)->firstOrFail();
        $cartItem = $cart->items()->findOrFail($cartItemId);

        return $cartItem->delete();
    }

    public function clearCart(string $userId): bool
    {
        $cart = Cart::where('user_id', $userId)->first();

        if ($cart) {
            $cart->items()->delete();

            return true;
        }

        return false;
    }

    public function itemExists(string $userId, string $cartItemId): bool
    {
        return Cart::where('user_id', $userId)
            ->whereHas('items', function ($query) use ($cartItemId) {
                $query->where('id', $cartItemId);
            })
            ->exists();
    }

    public function variantExists(string $productVariantId): bool
    {
        return ProductVariant::where('id', $productVariantId)->exists();
    }

    public function getItemByVariant(string $userId, string $productVariantId): ?array
    {
        $cart = Cart::where('user_id', $userId)->first();

        if (! $cart) {
            return null;
        }

        $item = $cart->items()->where('product_variant_id', $productVariantId)->first();

        return $item?->toArray();
    }

    private function formatCart(Cart $cart): array
    {
        return [
            'id' => $cart->id,
            'created_at' => $cart->created_at,
            'updated_at' => $cart->updated_at,
            'items' => $cart->items->map(fn ($item) => [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'product_variant' => [
                    'id' => $item->productVariant->id,
                    'sku' => $item->productVariant->sku,
                    'price' => $item->productVariant->price,
                    'original_price' => $item->productVariant->original_price,
                    'images' => $item->productVariant->images,
                    'options' => $item->productVariant->options,
                    'product' => [
                        'id' => $item->productVariant->product->id,
                        'name' => $item->productVariant->product->name,
                        'thumbnail' => $item->productVariant->product->thumbnail,
                    ],
                ],
            ])->toArray(),
        ];
    }
}
