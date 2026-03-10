<?php

namespace App\Domain\Shipping\DataTransferObjects;

class ShippingCalculationData
{
    public function __construct(
        public string $fromProvince,
        public string $fromDistrict,
        public string $toProvince,
        public string $toDistrict,
        public int $weight,
        public int $value,
        public string $transport = 'road'
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            fromProvince: $data['from_province'],
            fromDistrict: $data['from_district'],
            toProvince: $data['to_province'],
            toDistrict: $data['to_district'],
            weight: $data['weight'],
            value: $data['value'],
            transport: $data['transport'] ?? 'road'
        );
    }
}
