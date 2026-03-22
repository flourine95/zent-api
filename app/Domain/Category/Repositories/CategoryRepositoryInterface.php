<?php

namespace App\Domain\Category\Repositories;

interface CategoryRepositoryInterface
{
    public function create(array $data): array;

    public function update(string $id, array $data): array;

    public function delete(string $id): bool;

    public function findById(string $id): ?array;

    public function findBySlug(string $slug): ?array;

    public function exists(string $id): bool;

    public function slugExists(string $slug, ?string $excludeId = null): bool;

    public function isDescendantOf(string $categoryId, string $potentialParentId): bool;

    public function getAll(): array;

    public function getTree(): array;
}
