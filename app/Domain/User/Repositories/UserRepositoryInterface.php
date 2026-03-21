<?php

namespace App\Domain\User\Repositories;

interface UserRepositoryInterface
{
    public function create(array $data): array;

    public function update(int $id, array $data): array;

    public function delete(int $id): bool;

    public function findById(int $id): ?array;

    public function findByEmail(string $email): ?array;

    public function exists(int $id): bool;

    public function emailExists(string $email): bool;

    public function emailExistsExcept(string $email, int $exceptUserId): bool;

    public function verifyPassword(string $plainPassword, string $hashedPassword): bool;

    public function verifyPasswordByEmail(string $email, string $plainPassword): bool;

    public function updatePassword(int $userId, string $newPassword): bool;

    public function getAll(): array;

    public function createToken(int $userId, string $tokenName): string;

    public function revokeToken(int $userId, string $tokenId): bool;

    public function revokeAllTokens(int $userId): bool;
}
