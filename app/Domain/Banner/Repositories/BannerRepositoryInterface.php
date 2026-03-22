<?php

namespace App\Domain\Banner\Repositories;

interface BannerRepositoryInterface
{
    public function create(array $data): array;

    public function update(string $id, array $data): array;

    public function delete(string $id): bool;

    public function findById(string $id): ?array;

    public function exists(string $id): bool;

    public function getAll(): array;

    public function getActive(): array;

    public function getByPosition(string $position): array;
}
