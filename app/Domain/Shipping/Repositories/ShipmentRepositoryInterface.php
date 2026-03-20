<?php

namespace App\Domain\Shipping\Repositories;

interface ShipmentRepositoryInterface
{
    public function findByOrderId(int $orderId): ?array;

    public function create(array $data): array;

    public function updateStatus(int $shipmentId, string $status, ?string $providerStatus, ?string $note): array;

    public function cancel(int $shipmentId): array;
}
