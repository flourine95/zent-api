<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Warehouse\Repositories\WarehouseRepositoryInterface;
use App\Infrastructure\Models\Warehouse;

class EloquentWarehouseRepository implements WarehouseRepositoryInterface
{
    public function getAll(): array
    {
        return Warehouse::query()
            ->orderBy('name')
            ->get()
            ->map(fn ($warehouse) => $this->toArray($warehouse))
            ->toArray();
    }

    public function getActive(): array
    {
        return Warehouse::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn ($warehouse) => $this->toArray($warehouse))
            ->toArray();
    }

    public function findById(int $id): ?array
    {
        $warehouse = Warehouse::find($id);

        return $warehouse ? $this->toArray($warehouse) : null;
    }

    public function findByCode(string $code): ?array
    {
        $warehouse = Warehouse::where('code', $code)->first();

        return $warehouse ? $this->toArray($warehouse) : null;
    }

    protected function toArray(Warehouse $warehouse): array
    {
        return [
            'id' => $warehouse->id,
            'name' => $warehouse->name,
            'code' => $warehouse->code,
            'address' => $warehouse->address,
            'is_active' => $warehouse->is_active,
            'created_at' => $warehouse->created_at?->toISOString(),
            'updated_at' => $warehouse->updated_at?->toISOString(),
        ];
    }
}
