<?php

namespace App\Domain\Cart\Repositories;

interface CartRepositoryInterface
{
    public function getOrCreateByUserId(int $userId): array;

    public function getByUserIdWithItems(int $userId): array;

    public function addItem(int $userId, int $productVariantId, int $quantity): array;

    public function updateItem(int $userId, int $cartItemId, int $quantity): array;

    public function removeItem(int $userId, int $cartItemId): bool;

    public function clearCart(int $userId): bool;

    public function itemExists(int $userId, int $cartItemId): bool;

    public function variantExists(int $productVariantId): bool;

    public function getItemByVariant(int $userId, int $productVariantId): ?array;
}
