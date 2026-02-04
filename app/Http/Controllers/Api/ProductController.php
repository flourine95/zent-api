<?php

namespace App\Http\Controllers\Api;

use App\Helpers\LocaleHelper;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Lấy danh sách sản phẩm với translation theo locale
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->input('locale', LocaleHelper::default());
        
        // Validate locale
        if (!LocaleHelper::isValid($locale)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid locale',
                'available_locales' => LocaleHelper::codes(),
            ], 400);
        }

        // Set locale cho request này
        app()->setLocale($locale);

        $products = Product::with('category')
            ->where('is_active', true)
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'data' => $products->map(function ($product) use ($locale) {
                return [
                    'id' => $product->id,
                    'name' => $product->getTranslation('name', $locale),
                    'description' => $product->getTranslation('description', $locale),
                    'slug' => $product->slug,
                    'thumbnail' => $product->thumbnail ? asset('storage/' . $product->thumbnail) : null,
                    'specs' => $product->specs,
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->getTranslation('name', $locale),
                    ] : null,
                    'created_at' => $product->created_at,
                ];
            }),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * Lấy chi tiết sản phẩm theo slug hoặc ID
     */
    public function show(Request $request, string $identifier): JsonResponse
    {
        $locale = $request->input('locale', LocaleHelper::default());
        
        // Validate locale
        if (!LocaleHelper::isValid($locale)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid locale',
                'available_locales' => LocaleHelper::codes(),
            ], 400);
        }

        // Set locale
        app()->setLocale($locale);

        // Tìm theo slug hoặc ID
        $product = is_numeric($identifier)
            ? Product::find($identifier)
            : Product::where('slug', $identifier)->first();

        if (!$product || !$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $product->load(['category', 'variants']);

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'data' => [
                'id' => $product->id,
                'name' => $product->getTranslation('name', $locale),
                'description' => $product->getTranslation('description', $locale),
                'slug' => $product->slug,
                'thumbnail' => $product->thumbnail ? asset('storage/' . $product->thumbnail) : null,
                'specs' => $product->specs,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->getTranslation('name', $locale),
                    'slug' => $product->category->slug,
                ] : null,
                'variants' => $product->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'sku' => $variant->sku,
                        'price' => $variant->price,
                        'attributes' => $variant->attributes,
                    ];
                }),
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ],
        ]);
    }

    /**
     * Lấy tất cả translations của một sản phẩm
     */
    public function translations(string $identifier): JsonResponse
    {
        // Tìm theo slug hoặc ID
        $product = is_numeric($identifier)
            ? Product::find($identifier)
            : Product::where('slug', $identifier)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $translations = [];
        foreach (LocaleHelper::codes() as $locale) {
            $translations[$locale] = [
                'name' => $product->getTranslation('name', $locale, false),
                'description' => $product->getTranslation('description', $locale, false),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'slug' => $product->slug,
                'translations' => $translations,
                'available_locales' => LocaleHelper::codes(),
            ],
        ]);
    }
}
