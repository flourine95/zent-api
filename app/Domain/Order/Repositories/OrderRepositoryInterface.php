<?php

namespace App\Domain\Order\Repositories;

interface OrderRepositoryInterface
{
    public function create(array $orderData, array $items): array;

    public function update(int $id, array $data): array;

    public function delete(int $id): bool;

    public function findById(int $id): ?array;

    public function findByCode(string $code): ?array;

    public function exists(int $id): bool;

    public function getByUserId(int $userId): array;

    public function getAll(array $filters = []): array;
}
