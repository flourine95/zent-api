<?php

namespace App\Domain\Product\Repositories;

interface ProductRepositoryInterface
{
    public function create(array $data): array;

    public function update(int $id, array $data): array;

    public function delete(int $id): bool;

    public function findById(int $id): ?array;

    public function findBySlug(string $slug): ?array;

    public function exists(int $id): bool;

    public function slugExists(string $slug, ?int $excludeId = null): bool;

    public function getAll(array $filters = []): array;

    public function paginate(array $filters = [], int $perPage = 15, int $page = 1): array;

    public function getByCategoryId(int $categoryId): array;

    public function getActive(): array;
}
