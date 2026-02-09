<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\OrderResource;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Lấy danh sách đơn hàng của user
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->with(['items'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    /**
     * Tạo đơn hàng mới
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|array',
            'billing_address' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Calculate total
            $totalAmount = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $variant = ProductVariant::findOrFail($item['product_variant_id']);
                $subtotal = $variant->price * $item['quantity'];
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'product_variant_id' => $variant->id,
                    'quantity' => $item['quantity'],
                    'price' => $variant->price,
                    'subtotal' => $subtotal,
                ];
            }

            // Create order
            $order = $request->user()->orders()->create([
                'code' => 'ORD-'.strtoupper(uniqid()),
                'status' => 'pending',
                'payment_status' => 'pending',
                'total_amount' => $totalAmount,
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->billing_address ?? $request->shipping_address,
                'notes' => $request->notes,
            ]);

            // Create order items
            $order->items()->createMany($orderItems);

            DB::commit();

            $order->load(['items']);

            return response()->json([
                'success' => true,
                'message' => 'Đơn hàng đã được tạo thành công',
                'data' => new OrderResource($order),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo đơn hàng',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy chi tiết đơn hàng
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        // Check ownership
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập đơn hàng này',
            ], 403);
        }

        $order->load(['items']);

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order),
        ]);
    }
}
