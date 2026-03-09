<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function __construct(
        protected ShippingService $shippingService
    ) {}

    /**
     * Calculate shipping fees for all available providers
     */
    public function calculateFees(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_province' => 'required|string',
            'from_district' => 'required|string',
            'to_province' => 'required|string',
            'to_district' => 'required|string',
            'weight' => 'required|integer|min:1',
            'value' => 'required|integer|min:0',
            'transport' => 'nullable|in:fly,road',
        ]);

        try {
            $fees = $this->shippingService->compareShippingFees([
                'pick_province' => $validated['from_province'],
                'pick_district' => $validated['from_district'],
                'province' => $validated['to_province'],
                'district' => $validated['to_district'],
                'weight' => $validated['weight'],
                'value' => $validated['value'],
                'transport' => $validated['transport'] ?? 'road',
            ]);

            return response()->json([
                'success' => true,
                'data' => $fees,
                'cheapest' => $fees[0] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available shipping providers
     */
    public function getProviders(): JsonResponse
    {
        $providers = \App\Models\ShippingProvider::active()
            ->orderBy('priority')
            ->get()
            ->map(fn ($provider) => [
                'code' => $provider->code,
                'name' => $provider->name,
                'priority' => $provider->priority,
            ]);

        return response()->json([
            'success' => true,
            'data' => $providers,
        ]);
    }

    /**
     * Get shipping settings
     */
    public function getSettings(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'allow_user_selection' => config('shipping.allow_user_selection', true),
                'auto_select_criteria' => config('shipping.auto_select_criteria', 'cheapest'),
                'show_provider_details' => config('shipping.show_provider_details', true),
            ],
        ]);
    }
}
