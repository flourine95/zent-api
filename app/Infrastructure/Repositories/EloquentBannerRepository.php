<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Banner\Repositories\BannerRepositoryInterface;
use App\Infrastructure\Models\Banner;

final class EloquentBannerRepository implements BannerRepositoryInterface
{
    public function create(array $data): array
    {
        $banner = Banner::create($data);

        return $banner->toArray();
    }

    public function update(int $id, array $data): array
    {
        $banner = Banner::findOrFail($id);
        $banner->update($data);

        return $banner->fresh()->toArray();
    }

    public function delete(int $id): bool
    {
        $banner = Banner::findOrFail($id);

        return $banner->delete();
    }

    public function findById(int $id): ?array
    {
        $banner = Banner::find($id);

        return $banner?->toArray();
    }

    public function exists(int $id): bool
    {
        return Banner::where('id', $id)->exists();
    }

    public function getAll(): array
    {
        return Banner::orderBy('order')->orderBy('created_at', 'desc')->get()->toArray();
    }

    public function getActive(): array
    {
        return Banner::active()->orderBy('order')->get()->toArray();
    }

    public function getByPosition(string $position): array
    {
        return Banner::active()
            ->position($position)
            ->orderBy('order')
            ->get()
            ->toArray();
    }
}
