<?php

namespace App\Domain\Shipping\Repositories;

interface ShipmentRepositoryInterface
{
    public function findByOrderId(string $orderId): ?array;

    public function create(array $data): array;

    public function updateStatus(string $shipmentId, string $status, ?string $providerStatus, ?string $note): array;

    public function cancel(string $shipmentId): array;
}
