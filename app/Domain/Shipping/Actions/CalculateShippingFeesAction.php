<?php

namespace App\Domain\Shipping\Actions;

use App\Domain\Shipping\DataTransferObjects\ShippingCalculationData;
use App\Domain\Shipping\Repositories\ShippingRepositoryInterface;

class CalculateShippingFeesAction
{
    public function __construct(
        protected ShippingRepositoryInterface $shippingRepository
    ) {}

    public function execute(ShippingCalculationData $data): array
    {
        $fees = $this->shippingRepository->calculateFees($data->toParams());

        return [
            'fees' => $fees,
            'cheapest' => $fees[0] ?? null,
        ];
    }
}
