<?php

namespace App\Domain\User\Actions;

use App\Domain\User\Exceptions\InvalidCredentialsException;
use App\Domain\User\Exceptions\UserNotFoundException;
use App\Domain\User\Repositories\UserRepositoryInterface;

final readonly class ChangePasswordAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * @throws UserNotFoundException
     * @throws InvalidCredentialsException
     */
    public function execute(int $userId, string $currentPassword, string $newPassword): bool
    {
        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw UserNotFoundException::withId($userId);
        }

        // Verify current password
        if (! $this->userRepository->verifyPassword($currentPassword, $user['password'])) {
            throw InvalidCredentialsException::incorrectCurrentPassword();
        }

        // Update password
        return $this->userRepository->updatePassword($userId, $newPassword);
    }
}
