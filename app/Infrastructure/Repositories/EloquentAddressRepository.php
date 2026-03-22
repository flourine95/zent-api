<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Address\Repositories\AddressRepositoryInterface;
use App\Infrastructure\Models\Address;

final class EloquentAddressRepository implements AddressRepositoryInterface
{
    public function getAllByUserId(int $userId): array
    {
        return Address::where('user_id', $userId)
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get()
            ->toArray();
    }

    public function create(array $data): array
    {
        $address = Address::create($data);

        return $address->toArray();
    }

    public function update(int $id, array $data): array
    {
        $address = Address::findOrFail($id);
        $address->update($data);

        return $address->fresh()->toArray();
    }

    public function delete(int $id): bool
    {
        $address = Address::findOrFail($id);

        return $address->delete();
    }

    public function findById(int $id): ?array
    {
        $address = Address::find($id);

        return $address?->toArray();
    }

    public function exists(int $id): bool
    {
        return Address::where('id', $id)->exists();
    }

    public function belongsToUser(int $addressId, int $userId): bool
    {
        return Address::where('id', $addressId)
            ->where('user_id', $userId)
            ->exists();
    }

    public function setAsDefault(int $userId, int $addressId): array
    {
        Address::where('user_id', $userId)->update(['is_default' => false]);

        $address = Address::findOrFail($addressId);
        $address->update(['is_default' => true]);

        return $address->fresh()->toArray();
    }

    public function getDefaultByUserId(int $userId): ?array
    {
        $address = Address::where('user_id', $userId)
            ->where('is_default', true)
            ->first();

        return $address?->toArray();
    }

    public function unsetAllDefaults(int $userId): bool
    {
        Address::where('user_id', $userId)->update(['is_default' => false]);

        return true;
    }

    public function unsetOtherDefaults(int $userId, int $exceptAddressId): bool
    {
        Address::where('user_id', $userId)
            ->where('id', '!=', $exceptAddressId)
            ->update(['is_default' => false]);

        return true;
    }
}
