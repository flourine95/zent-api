<?php

namespace App\App\Shipping\Controllers;

use App\App\Shipping\Requests\CalculateShippingFeesRequest;
use App\Domain\Shipping\Actions\CalculateShippingFeesAction;
use App\Domain\Shipping\Actions\GetShippingProvidersAction;
use App\Domain\Shipping\Actions\GetShippingSettingsAction;
use App\Domain\Shipping\DataTransferObjects\ShippingCalculationData;
use App\Shared\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

final readonly class ShippingController
{
    use ApiResponse;

    public function __construct(
        private CalculateShippingFeesAction $calculateShippingFeesAction,
        private GetShippingProvidersAction $getShippingProvidersAction,
        private GetShippingSettingsAction $getShippingSettingsAction,
    ) {}

    public function calculateFees(CalculateShippingFeesRequest $request): JsonResponse
    {
        try {
            $data = ShippingCalculationData::fromArray($request->validated());
            $result = $this->calculateShippingFeesAction->execute($data);

            return $this->success($result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 'SHIPPING_CALCULATION_FAILED', 500);
        }
    }

    public function getProviders(): JsonResponse
    {
        try {
            return $this->success($this->getShippingProvidersAction->execute());
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 'SHIPPING_PROVIDERS_UNAVAILABLE', 500);
        }
    }

    public function getSettings(): JsonResponse
    {
        return $this->success($this->getShippingSettingsAction->execute());
    }
}
