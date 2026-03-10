<?php

namespace App\Domain\Address\Repositories;

interface AddressRepositoryInterface
{
    public function getAllByUserId(int $userId): array;

    public function create(array $data): array;

    public function update(int $id, array $data): array;

    public function delete(int $id): bool;

    public function findById(int $id): ?array;

    public function exists(int $id): bool;

    public function belongsToUser(int $addressId, int $userId): bool;

    public function setAsDefault(int $userId, int $addressId): array;

    public function unsetAllDefaults(int $userId): bool;

    public function unsetOtherDefaults(int $userId, int $exceptAddressId): bool;
}
