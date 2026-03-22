<?php

namespace App\Domain\Address\Actions;

use App\Domain\Address\Exceptions\AddressNotFoundException;
use App\Domain\Address\Exceptions\UnauthorizedAddressAccessException;
use App\Domain\Address\Repositories\AddressRepositoryInterface;

final readonly class SetDefaultAddressAction
{
    public function __construct(
        private AddressRepositoryInterface $addressRepository
    ) {}

    /**
     * @throws AddressNotFoundException
     * @throws UnauthorizedAddressAccessException
     */
    public function execute(string $userId, string $addressId): array
    {
        if (! $this->addressRepository->exists($addressId)) {
            throw AddressNotFoundException::withId($addressId);
        }

        if (! $this->addressRepository->belongsToUser($addressId, $userId)) {
            throw UnauthorizedAddressAccessException::forUser($userId, $addressId);
        }

        return $this->addressRepository->setAsDefault($userId, $addressId);
    }
}
