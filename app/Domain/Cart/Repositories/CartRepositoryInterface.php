<?php

namespace App\Domain\Cart\Repositories;

interface CartRepositoryInterface
{
    public function getOrCreateByUserId(string $userId): array;

    public function getByUserIdWithItems(string $userId): array;

    public function addItem(string $userId, string $productVariantId, int $quantity): array;

    public function updateItem(string $userId, string $cartItemId, int $quantity): array;

    public function removeItem(string $userId, string $cartItemId): bool;

    public function clearCart(string $userId): bool;

    public function itemExists(string $userId, string $cartItemId): bool;

    public function variantExists(string $productVariantId): bool;

    public function getItemByVariant(string $userId, string $productVariantId): ?array;
}
