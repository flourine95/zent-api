<?php

namespace App\Domain\Address\Repositories;

interface AddressRepositoryInterface
{
    public function getAllByUserId(string $userId): array;

    public function create(array $data): array;

    public function update(string $id, array $data): array;

    public function delete(string $id): bool;

    public function findById(string $id): ?array;

    public function exists(string $id): bool;

    public function belongsToUser(string $addressId, string $userId): bool;

    public function setAsDefault(string $userId, string $addressId): array;

    public function getDefaultByUserId(string $userId): ?array;

    public function unsetAllDefaults(string $userId): bool;

    public function unsetOtherDefaults(string $userId, string $exceptAddressId): bool;
}
