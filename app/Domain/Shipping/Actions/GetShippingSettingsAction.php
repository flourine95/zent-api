<?php

namespace App\Domain\Shipping\Actions;

use App\Domain\Shipping\Repositories\ShippingRepositoryInterface;

class GetShippingSettingsAction
{
    public function __construct(
        protected ShippingRepositoryInterface $shippingRepository
    ) {}

    public function execute(): array
    {
        return $this->shippingRepository->getShippingSettings();
    }
}
