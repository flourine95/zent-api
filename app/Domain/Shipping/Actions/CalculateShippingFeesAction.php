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
        $params = [
            'pick_province' => $data->fromProvince,
            'pick_district' => $data->fromDistrict,
            'province' => $data->toProvince,
            'district' => $data->toDistrict,
            'weight' => $data->weight,
            'value' => $data->value,
            'transport' => $data->transport,
        ];

        $fees = $this->shippingRepository->calculateFees($params);

        return [
            'fees' => $fees,
            'cheapest' => $fees[0] ?? null,
        ];
    }
}
