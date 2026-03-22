<?php

namespace App\Domain\Product\Repositories;

interface ProductRepositoryInterface
{
    public function create(array $data): array;

    public function update(string $id, array $data): array;

    public function delete(string $id): bool;

    public function findById(string $id): ?array;

    public function findBySlug(string $slug): ?array;

    public function exists(string $id): bool;

    public function slugExists(string $slug, ?string $excludeId = null): bool;

    public function getAll(array $filters = []): array;

    public function paginate(array $filters = [], int $perPage = 15, int $page = 1): array;

    public function getByCategoryId(string $categoryId): array;

    public function getActive(): array;
}
