<?php

namespace App\Domain\Address\Actions;

use App\Domain\Address\Repositories\AddressRepositoryInterface;

final readonly class GetUserAddressesAction
{
    public function __construct(
        private AddressRepositoryInterface $addressRepository
    ) {}

    public function execute(int $userId): array
    {
        return $this->addressRepository->getAllByUserId($userId);
    }
}
