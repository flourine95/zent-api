<?php

namespace App\Domain\Address\Actions;

use App\Domain\Address\DataTransferObjects\UpdateAddressData;
use App\Domain\Address\Exceptions\AddressNotFoundException;
use App\Domain\Address\Exceptions\UnauthorizedAddressAccessException;
use App\Domain\Address\Repositories\AddressRepositoryInterface;

final readonly class UpdateAddressAction
{
    public function __construct(
        private AddressRepositoryInterface $addressRepository
    ) {}

    /**
     * @throws AddressNotFoundException
     * @throws UnauthorizedAddressAccessException
     */
    public function execute(UpdateAddressData $data): array
    {
        // Validate address exists
        if (! $this->addressRepository->exists($data->id)) {
            throw AddressNotFoundException::withId($data->id);
        }

        // Validate ownership
        if (! $this->addressRepository->belongsToUser($data->id, $data->userId)) {
            throw UnauthorizedAddressAccessException::forUser($data->userId, $data->id);
        }

        // If this is set as default, unset other defaults
        if ($data->isDefault) {
            $this->addressRepository->unsetOtherDefaults($data->userId, $data->id);
        }

        return $this->addressRepository->update($data->id, $data->toArray());
    }
}
