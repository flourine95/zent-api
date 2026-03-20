<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Infrastructure\Models\Product;

final class EloquentProductRepository implements ProductRepositoryInterface
{
    public function create(array $data): array
    {
        $product = Product::create($data);

        return $product->toArray();
    }

    public function update(int $id, array $data): array
    {
        $product = Product::findOrFail($id);
        $product->update($data);

        return $product->fresh()->toArray();
    }

    public function delete(int $id): bool
    {
        $product = Product::findOrFail($id);

        return $product->delete();
    }

    public function findById(int $id): ?array
    {
        $product = Product::with(['category', 'variants'])->find($id);

        return $product?->toArray();
    }

    public function findBySlug(string $slug): ?array
    {
        $product = Product::with(['category', 'variants'])
            ->where('slug', $slug)
            ->first();

        return $product?->toArray();
    }

    public function exists(int $id): bool
    {
        return Product::where('id', $id)->exists();
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = Product::where('slug', $slug);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function getAll(array $filters = []): array
    {
        $query = Product::with(['category', 'variants']);

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->get()->toArray();
    }

    public function paginate(array $filters = [], int $perPage = 15, int $page = 1): array
    {
        $query = Product::with(['category', 'variants']);

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        $paginator = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];
    }

    public function getByCategoryId(int $categoryId): array
    {
        return Product::with(['variants'])
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function getActive(): array
    {
        return Product::with(['category', 'variants'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
}
