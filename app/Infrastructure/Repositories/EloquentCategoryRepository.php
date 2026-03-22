<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Infrastructure\Models\Category;

final class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function create(array $data): array
    {
        $category = Category::create($data);

        return $category->toArray();
    }

    public function update(string $id, array $data): array
    {
        $category = Category::findOrFail($id);
        $category->update($data);

        return $category->fresh()->toArray();
    }

    public function delete(string $id): bool
    {
        $category = Category::findOrFail($id);

        return $category->delete();
    }

    public function findById(string $id): ?array
    {
        $category = Category::find($id);

        return $category?->toArray();
    }

    public function findBySlug(string $slug): ?array
    {
        $category = Category::where('slug', $slug)->first();

        return $category?->toArray();
    }

    public function exists(string $id): bool
    {
        return Category::where('id', $id)->exists();
    }

    public function slugExists(string $slug, ?string $excludeId = null): bool
    {
        $query = Category::where('slug', $slug);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function isDescendantOf(string $categoryId, string $potentialParentId): bool
    {
        $category = Category::find($categoryId);

        if (! $category) {
            return false;
        }

        return $this->hasDescendant($category, $potentialParentId);
    }

    public function getAll(): array
    {
        return Category::orderBy('name')->get()->toArray();
    }

    public function getTree(): array
    {
        return Category::tree()->toArray();
    }

    private function hasDescendant(Category $category, string $targetId): bool
    {
        $children = $category->children;

        foreach ($children as $child) {
            if ($child->id === $targetId) {
                return true;
            }

            if ($this->hasDescendant($child, $targetId)) {
                return true;
            }
        }

        return false;
    }
}
