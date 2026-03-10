<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\ProductResource;
use App\Infrastructure\Models\Banner;
use App\Infrastructure\Models\Category;
use App\Infrastructure\Models\Product;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ConfigController extends Controller
{
    /**
     * Get app configuration and initial data
     * Cached for 1 hour
     */
    public function index(): JsonResponse
    {
        $config = Cache::remember('api.config', 3600, function () {
            return [
                'app' => [
                    'name' => config('app.name'),
                    'locale' => config('app.locale'),
                    'currency' => 'VND',
                    'timezone' => config('app.timezone'),
                ],
                'settings' => [
                    'shipping_fee' => Setting::get('shipping_fee', 30000),
                    'free_shipping_threshold' => Setting::get('free_shipping_threshold', 500000),
                    'contact_email' => Setting::get('contact_email', 'support@example.com'),
                    'contact_phone' => Setting::get('contact_phone', '1900-xxxx'),
                ],
                'banners' => Banner::active()
                    ->orderBy('order')
                    ->get()
                    ->groupBy('position')
                    ->map(fn ($banners) => $banners->map(fn ($banner) => [
                        'id' => $banner->id,
                        'title' => $banner->title,
                        'description' => $banner->description,
                        'image' => $banner->image ? asset("storage/{$banner->image}") : null,
                        'link' => $banner->link,
                        'button_text' => $banner->button_text,
                    ])),
                'categories' => CategoryResource::collection(
                    Category::with(['children'])
                        ->where('is_visible', true)
                        ->whereNull('parent_id')
                        ->orderBy('name')
                        ->get()
                ),
                'featured_products' => ProductResource::collection(
                    Product::with(['category', 'variants'])
                        ->where('is_active', true)
                        ->latest()
                        ->limit(8)
                        ->get()
                ),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $config,
        ]);
    }
}
