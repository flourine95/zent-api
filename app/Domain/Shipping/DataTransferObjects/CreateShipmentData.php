<?php

namespace App\Domain\Shipping\DataTransferObjects;

class CreateShipmentData
{
    public function __construct(
        public readonly int $orderId,
        public readonly string $providerCode,
        public readonly array $orderData,
    ) {}

    public static function fromArray(int $orderId, array $data): self
    {
        return new self(
            orderId: $orderId,
            providerCode: $data['provider_code'],
            orderData: $data['order_data'],
        );
    }
}
