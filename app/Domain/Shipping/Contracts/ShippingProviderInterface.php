<?php

namespace App\Domain\Shipping\Contracts;

interface ShippingProviderInterface
{
    /**
     * Create a shipment order
     */
    public function createOrder(array $orderData): array;

    /**
     * Calculate shipping fee
     */
    public function calculateFee(array $params): array;

    /**
     * Get shipment status
     */
    public function getStatus(string $providerOrderId): array;

    /**
     * Cancel a shipment
     */
    public function cancel(string $providerOrderId): bool;

    /**
     * Get print label URL
     */
    public function getPrintLabelUrl(string $labelId): ?string;

    /**
     * Normalize provider status to standard status
     */
    public function normalizeStatus(int|string $providerStatus): string;

    /**
     * Get provider code
     */
    public function getProviderCode(): string;
}
