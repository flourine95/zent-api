<?php

namespace App\Infrastructure\Repositories;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Infrastructure\Models\User;
use Illuminate\Support\Facades\Hash;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function create(array $data): array
    {
        // Hash password before creating
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = User::create($data);

        return $user->toArray();
    }

    public function update(int $id, array $data): array
    {
        $user = User::findOrFail($id);
        $user->update($data);

        return $user->fresh()->toArray();
    }

    public function delete(int $id): bool
    {
        $user = User::findOrFail($id);

        return $user->delete();
    }

    public function findById(int $id): ?array
    {
        $user = User::find($id);

        return $user?->toArray();
    }

    public function findByEmail(string $email): ?array
    {
        $user = User::where('email', $email)->first();

        return $user?->toArray();
    }

    public function exists(int $id): bool
    {
        return User::where('id', $id)->exists();
    }

    public function emailExists(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    public function emailExistsExcept(string $email, int $exceptUserId): bool
    {
        return User::where('email', $email)
            ->where('id', '!=', $exceptUserId)
            ->exists();
    }

    public function verifyPassword(string $plainPassword, string $hashedPassword): bool
    {
        return Hash::check($plainPassword, $hashedPassword);
    }

    public function verifyPasswordByEmail(string $email, string $plainPassword): bool
    {
        $user = User::where('email', $email)->first();

        if ($user === null) {
            return false;
        }

        return Hash::check($plainPassword, $user->password);
    }

    public function updatePassword(int $userId, string $newPassword): bool
    {
        $user = User::findOrFail($userId);

        return $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }

    public function getAll(): array
    {
        return User::orderBy('created_at', 'desc')->get()->toArray();
    }

    public function createToken(int $userId, string $tokenName): string
    {
        $user = User::findOrFail($userId);

        return $user->createToken($tokenName)->plainTextToken;
    }

    public function revokeToken(int $userId, string $tokenId): bool
    {
        $user = User::findOrFail($userId);

        return $user->tokens()->where('id', $tokenId)->delete() > 0;
    }

    public function revokeAllTokens(int $userId): bool
    {
        $user = User::findOrFail($userId);
        $user->tokens()->delete();

        return true;
    }
}
