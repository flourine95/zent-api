<?php

namespace App\Domain\User\Actions;

use App\Domain\User\DataTransferObjects\UpdateProfileData;
use App\Domain\User\Exceptions\EmailAlreadyExistsException;
use App\Domain\User\Exceptions\UserNotFoundException;
use App\Domain\User\Repositories\UserRepositoryInterface;

final readonly class UpdateProfileAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * @throws UserNotFoundException
     * @throws EmailAlreadyExistsException
     */
    public function execute(UpdateProfileData $data): array
    {
        if (! $this->userRepository->exists($data->userId)) {
            throw UserNotFoundException::withId($data->userId);
        }

        // Check if email is taken by another user
        if ($this->userRepository->emailExistsExcept($data->email, $data->userId)) {
            throw EmailAlreadyExistsException::forEmail($data->email);
        }

        return $this->userRepository->update($data->userId, $data->toArray());
    }
}
