<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Address\Repositories\AddressRepositoryInterface;
use App\Infrastructure\Models\Address;

final class EloquentAddressRepository implements AddressRepositoryInterface
{
    public function getAllByUserId(string $userId): array
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

    public function update(string $id, array $data): array
    {
        $address = Address::findOrFail($id);
        $address->update($data);

        return $address->fresh()->toArray();
    }

    public function delete(string $id): bool
    {
        $address = Address::findOrFail($id);

        return $address->delete();
    }

    public function findById(string $id): ?array
    {
        $address = Address::find($id);

        return $address?->toArray();
    }

    public function exists(string $id): bool
    {
        return Address::where('id', $id)->exists();
    }

    public function belongsToUser(string $addressId, string $userId): bool
    {
        return Address::where('id', $addressId)
            ->where('user_id', $userId)
            ->exists();
    }

    public function setAsDefault(string $userId, string $addressId): array
    {
        Address::where('user_id', $userId)->update(['is_default' => false]);

        $address = Address::findOrFail($addressId);
        $address->update(['is_default' => true]);

        return $address->fresh()->toArray();
    }

    public function getDefaultByUserId(string $userId): ?array
    {
        $address = Address::where('user_id', $userId)
            ->where('is_default', true)
            ->first();

        return $address?->toArray();
    }

    public function unsetAllDefaults(string $userId): bool
    {
        Address::where('user_id', $userId)->update(['is_default' => false]);

        return true;
    }

    public function unsetOtherDefaults(string $userId, string $exceptAddressId): bool
    {
        Address::where('user_id', $userId)
            ->where('id', '!=', $exceptAddressId)
            ->update(['is_default' => false]);

        return true;
    }
}
