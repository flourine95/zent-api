<?php

namespace App\Domain\Shipping\DataTransferObjects;

class ShippingCalculationData
{
    public function __construct(
        // GHTK fields (string names)
        public string $fromProvince,
        public string $fromDistrict,
        public string $toProvince,
        public string $toDistrict,
        public int $weight,
        public int $value,
        public string $transport = 'road',
        public ?string $fromWard = null,
        public ?string $toWard = null,
        // GHN fields (integer IDs)
        public ?int $fromDistrictId = null,
        public ?string $fromWardCode = null,
        public ?int $toDistrictId = null,
        public ?string $toWardCode = null,
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
            transport: $data['transport'] ?? 'road',
            fromWard: $data['from_ward'] ?? null,
            toWard: $data['to_ward'] ?? null,
            fromDistrictId: $data['from_district_id'] ?? null,
            fromWardCode: $data['from_ward_code'] ?? null,
            toDistrictId: $data['to_district_id'] ?? null,
            toWardCode: $data['to_ward_code'] ?? null,
        );
    }

    public function toParams(): array
    {
        return [
            // GHTK
            'pick_province' => $this->fromProvince,
            'pick_district' => $this->fromDistrict,
            'pick_ward' => $this->fromWard,
            'province' => $this->toProvince,
            'district' => $this->toDistrict,
            'ward' => $this->toWard,
            // GHN
            'from_district_id' => $this->fromDistrictId,
            'from_ward_code' => $this->fromWardCode,
            'to_district_id' => $this->toDistrictId,
            'to_ward_code' => $this->toWardCode,
            // Common
            'weight' => $this->weight,
            'value' => $this->value,
            'transport' => $this->transport,
        ];
    }
}
