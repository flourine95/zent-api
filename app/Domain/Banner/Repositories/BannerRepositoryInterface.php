<?php

namespace App\Domain\Banner\Repositories;

interface BannerRepositoryInterface
{
    public function create(array $data): array;

    public function update(int $id, array $data): array;

    public function delete(int $id): bool;

    public function findById(int $id): ?array;

    public function exists(int $id): bool;

    public function getAll(): array;

    public function getActive(): array;

    public function getByPosition(string $position): array;
}
