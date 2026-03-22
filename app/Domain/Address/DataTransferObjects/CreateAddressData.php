<?php

namespace App\Domain\Address\DataTransferObjects;

final readonly class CreateAddressData
{
    public function __construct(
        public string $userId,
        public ?string $label,
        public string $recipientName,
        public string $phone,
        public string $addressLine1,
        public ?string $addressLine2,
        public string $city,
        public ?string $state,
        public string $postalCode,
        public ?string $country,
        public bool $isDefault,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            label: $data['label'] ?? null,
            recipientName: $data['recipient_name'],
            phone: $data['phone'],
            addressLine1: $data['address_line_1'],
            addressLine2: $data['address_line_2'] ?? null,
            city: $data['city'],
            state: $data['state'] ?? null,
            postalCode: $data['postal_code'],
            country: $data['country'] ?? null,
            isDefault: $data['is_default'] ?? false,
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'label' => $this->label,
            'recipient_name' => $this->recipientName,
            'phone' => $this->phone,
            'address_line_1' => $this->addressLine1,
            'address_line_2' => $this->addressLine2,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postalCode,
            'country' => $this->country,
            'is_default' => $this->isDefault,
        ];
    }
}
