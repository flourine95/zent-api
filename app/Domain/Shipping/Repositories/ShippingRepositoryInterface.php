<?php

namespace App\Domain\Shipping\Repositories;

interface ShippingRepositoryInterface
{
    public function calculateFees(array $params): array;

    public function getActiveProviders(): array;

    public function getShippingSettings(): array;
}
