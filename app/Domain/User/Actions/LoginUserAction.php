<?php

namespace App\Domain\User\Actions;

use App\Domain\User\DataTransferObjects\LoginUserData;
use App\Domain\User\Exceptions\InvalidCredentialsException;
use App\Domain\User\Repositories\UserRepositoryInterface;

final readonly class LoginUserAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * @throws InvalidCredentialsException
     */
    public function execute(LoginUserData $data): array
    {
        $user = $this->userRepository->findByEmail($data->email);

        if ($user === null) {
            throw InvalidCredentialsException::invalidEmailOrPassword();
        }

        // Verify password
        if (! $this->userRepository->verifyPassword($data->password, $user['password'])) {
            throw InvalidCredentialsException::invalidEmailOrPassword();
        }

        return $user;
    }
}
