<?php

namespace App\Domain\Address\Actions;

use App\Domain\Address\DataTransferObjects\CreateAddressData;
use App\Domain\Address\Repositories\AddressRepositoryInterface;

final readonly class CreateAddressAction
{
    public function __construct(
        private AddressRepositoryInterface $addressRepository
    ) {}

    public function execute(CreateAddressData $data): array
    {
        // If this is set as default, unset other defaults
        if ($data->isDefault) {
            $this->addressRepository->unsetAllDefaults($data->userId);
        }

        return $this->addressRepository->create($data->toArray());
    }
}
