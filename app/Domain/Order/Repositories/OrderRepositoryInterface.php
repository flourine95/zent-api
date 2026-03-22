<?php

namespace App\Domain\Order\Repositories;

interface OrderRepositoryInterface
{
    public function create(array $orderData, array $items): array;

    /**
     * Create order with items and inventory reservations in a single transaction.
     */
    public function createWithReservations(array $orderData, array $items): array;

    public function update(string $id, array $data): array;

    public function delete(string $id): bool;

    public function findById(string $id): ?array;

    public function findByCode(string $code): ?array;

    public function exists(string $id): bool;

    public function getByUserId(string $userId): array;

    public function getAll(array $filters = []): array;

    public function paginate(array $filters = [], int $perPage = 15, int $page = 1): array;
}
