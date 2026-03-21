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

        if (! $this->userRepository->verifyPasswordByEmail($data->email, $data->password)) {
            throw InvalidCredentialsException::invalidEmailOrPassword();
        }

        return $user;
    }
}
