<?php

namespace App\App\Shipping\Controllers;

use App\App\Shipping\Requests\CalculateShippingFeesRequest;
use App\Domain\Shipping\Actions\CalculateShippingFeesAction;
use App\Domain\Shipping\Actions\GetShippingProvidersAction;
use App\Domain\Shipping\Actions\GetShippingSettingsAction;
use App\Domain\Shipping\DataTransferObjects\ShippingCalculationData;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ShippingController extends Controller
{
    public function __construct(
        protected CalculateShippingFeesAction $calculateShippingFeesAction,
        protected GetShippingProvidersAction $getShippingProvidersAction,
        protected GetShippingSettingsAction $getShippingSettingsAction
    ) {}

    public function calculateFees(CalculateShippingFeesRequest $request): JsonResponse
    {
        try {
            $data = ShippingCalculationData::fromArray($request->validated());
            $result = $this->calculateShippingFeesAction->execute($data);

            return response()->json([
                'success' => true,
                'data' => $result['fees'],
                'cheapest' => $result['cheapest'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getProviders(): JsonResponse
    {
        $providers = $this->getShippingProvidersAction->execute();

        return response()->json([
            'success' => true,
            'data' => $providers,
        ]);
    }

    public function getSettings(): JsonResponse
    {
        $settings = $this->getShippingSettingsAction->execute();

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }
}
