<?php

namespace App\Domain\Order\DataTransferObjects;

final readonly class UpdateOrderData
{
    public function __construct(
        public string $id,
        public string $status,
        public string $paymentStatus,
        public ?string $notes,
    ) {}

    public static function fromArray(string $id, array $data): self
    {
        return new self(
            id: $id,
            status: $data['status'],
            paymentStatus: $data['payment_status'],
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'payment_status' => $this->paymentStatus,
            'notes' => $this->notes,
        ];
    }
}
