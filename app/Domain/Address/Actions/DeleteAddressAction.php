<?php

namespace App\Domain\Address\Actions;

use App\Domain\Address\Exceptions\AddressNotFoundException;
use App\Domain\Address\Exceptions\UnauthorizedAddressAccessException;
use App\Domain\Address\Repositories\AddressRepositoryInterface;

final readonly class DeleteAddressAction
{
    public function __construct(
        private AddressRepositoryInterface $addressRepository
    ) {}

    /**
     * @throws AddressNotFoundException
     * @throws UnauthorizedAddressAccessException
     */
    public function execute(int $userId, int $addressId): bool
    {
        if (! $this->addressRepository->exists($addressId)) {
            throw AddressNotFoundException::withId($addressId);
        }

        if (! $this->addressRepository->belongsToUser($addressId, $userId)) {
            throw UnauthorizedAddressAccessException::forUser($userId, $addressId);
        }

        $address = $this->addressRepository->findById($addressId);
        $deleted = $this->addressRepository->delete($addressId);

        // If deleted address was default, promote the next available address
        if ($deleted && ($address['is_default'] ?? false)) {
            $remaining = $this->addressRepository->getAllByUserId($userId);
            if (! empty($remaining)) {
                $this->addressRepository->setAsDefault($userId, $remaining[0]['id']);
            }
        }

        return $deleted;
    }
}
