<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\OrderResource;
use App\Infrastructure\Models\Order;
use App\Infrastructure\Models\ProductVariant;
use App\Notifications\OrderCreatedNotification;
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
     * Tạo đơn hàng mới với product snapshot và inventory reservation
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.warehouse_id' => 'nullable|exists:warehouses,id',
            'shipping_address' => 'required|array',
            'billing_address' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Calculate total and prepare order items with snapshot
            $totalAmount = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $variant = ProductVariant::with(['product.category'])->findOrFail($item['product_variant_id']);

                // Create product snapshot
                $snapshot = [
                    'product_id' => $variant->product_id,
                    'product_name' => $variant->product->name,
                    'product_slug' => $variant->product->slug,
                    'category_name' => $variant->product->category->name ?? null,
                    'variant_id' => $variant->id,
                    'sku' => $variant->sku,
                    'price' => $variant->price,
                    'original_price' => $variant->original_price,
                    'options' => $variant->options,
                    'images' => $variant->images,
                ];

                $subtotal = $variant->price * $item['quantity'];
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'product_variant_id' => $variant->id,
                    'warehouse_id' => $item['warehouse_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $variant->price,
                    'subtotal' => $subtotal,
                    'product_snapshot' => $snapshot,
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
            foreach ($orderItems as $itemData) {
                $orderItem = $order->items()->create($itemData);

                // Create inventory reservation (lock stock for 30 minutes)
                if ($itemData['warehouse_id']) {
                    $inventory = \App\Models\Inventory::where('product_variant_id', $itemData['product_variant_id'])
                        ->where('warehouse_id', $itemData['warehouse_id'])
                        ->first();

                    if ($inventory) {
                        \App\Models\InventoryReservation::create([
                            'inventory_id' => $inventory->id,
                            'order_id' => $order->id,
                            'quantity' => $itemData['quantity'],
                            'expires_at' => now()->addMinutes(30),
                        ]);
                    }
                }
            }

            DB::commit();

            // Send notification
            $request->user()->notify(new OrderCreatedNotification($order));

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
