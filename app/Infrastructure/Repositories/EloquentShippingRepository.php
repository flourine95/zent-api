<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Shipping\Repositories\ShippingRepositoryInterface;
use App\Infrastructure\Models\ShippingProvider;
use App\Infrastructure\Services\ShippingService;

class EloquentShippingRepository implements ShippingRepositoryInterface
{
    public function __construct(
        protected ShippingService $shippingService
    ) {}

    public function calculateFees(array $params): array
    {
        return $this->shippingService->compareShippingFees($params);
    }

    public function getActiveProviders(): array
    {
        return ShippingProvider::active()
            ->orderBy('priority')
            ->get()
            ->map(fn ($provider) => [
                'code' => $provider->code,
                'name' => $provider->name,
                'priority' => $provider->priority,
            ])
            ->toArray();
    }

    public function getShippingSettings(): array
    {
        return [
            'allow_user_selection' => config('shipping.allow_user_selection', true),
            'auto_select_criteria' => config('shipping.auto_select_criteria', 'cheapest'),
            'show_provider_details' => config('shipping.show_provider_details', true),
        ];
    }
}
