<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Lấy danh sách danh mục
     */
    public function index(Request $request): JsonResponse
    {
        $query = Category::with(['parent', 'children'])
            ->where('is_visible', true);

        // Only root categories
        if ($request->boolean('root_only')) {
            $query->whereNull('parent_id');
        }

        // Filter by parent
        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        $categories = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories),
        ]);
    }

    /**
     * Lấy chi tiết danh mục
     */
    public function show(string $identifier): JsonResponse
    {
        $category = is_numeric($identifier)
            ? Category::find($identifier)
            : Category::where('slug', $identifier)->first();

        if (! $category || ! $category->is_visible) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy danh mục',
            ], 404);
        }

        $category->load(['parent', 'children']);

        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category),
        ]);
    }
}
