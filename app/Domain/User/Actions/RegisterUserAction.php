<?php

namespace App\Domain\User\Actions;

use App\Domain\User\DataTransferObjects\RegisterUserData;
use App\Domain\User\Exceptions\EmailAlreadyExistsException;
use App\Domain\User\Repositories\UserRepositoryInterface;

final readonly class RegisterUserAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * @throws EmailAlreadyExistsException
     */
    public function execute(RegisterUserData $data): array
    {
        // Check if email already exists
        if ($this->userRepository->emailExists($data->email)) {
            throw EmailAlreadyExistsException::forEmail($data->email);
        }

        // Create user with hashed password
        return $this->userRepository->create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password, // Will be hashed in repository
        ]);
    }
}
