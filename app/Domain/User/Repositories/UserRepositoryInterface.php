<?php

namespace App\Domain\User\Repositories;

interface UserRepositoryInterface
{
    public function create(array $data): array;

    public function update(string $id, array $data): array;

    public function delete(string $id): bool;

    public function findById(string $id): ?array;

    public function findByEmail(string $email): ?array;

    public function exists(string $id): bool;

    public function emailExists(string $email): bool;

    public function emailExistsExcept(string $email, string $exceptUserId): bool;

    public function verifyPassword(string $plainPassword, string $hashedPassword): bool;

    public function verifyPasswordByEmail(string $email, string $plainPassword): bool;

    public function updatePassword(string $userId, string $newPassword): bool;

    public function getAll(): array;

    public function createToken(string $userId, string $tokenName): string;

    public function revokeToken(string $userId, string $tokenId): bool;

    public function revokeAllTokens(string $userId): bool;
}
