<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Config\Repositories\ConfigRepositoryInterface;
use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\ProductResource;
use App\Infrastructure\Models\Banner;
use App\Infrastructure\Models\Category;
use App\Infrastructure\Models\Product;
use App\Infrastructure\Models\Setting;
use Illuminate\Support\Facades\Cache;

final class EloquentConfigRepository implements ConfigRepositoryInterface
{
    public function getAppConfig(): array
    {
        return Cache::remember('api.config', 3600, function () {
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
                    ]))
                    ->toArray(),
                'categories' => CategoryResource::collection(
                    Category::with(['children'])
                        ->where('is_visible', true)
                        ->whereNull('parent_id')
                        ->orderBy('name')
                        ->get()
                )->toArray(request()),
                'featured_products' => ProductResource::collection(
                    Product::with(['category', 'variants'])
                        ->where('is_active', true)
                        ->latest()
                        ->limit(8)
                        ->get()
                )->toArray(request()),
            ];
        });
    }
}
