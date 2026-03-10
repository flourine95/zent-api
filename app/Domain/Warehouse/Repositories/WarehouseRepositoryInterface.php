<?php

namespace App\Domain\Warehouse\Repositories;

interface WarehouseRepositoryInterface
{
    public function getAll(): array;

    public function getActive(): array;

    public function findById(int $id): ?array;

    public function findByCode(string $code): ?array;
}
