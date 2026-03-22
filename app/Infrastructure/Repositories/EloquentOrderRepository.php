<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Inventory\Exceptions\InsufficientStockException;
use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Infrastructure\Models\Inventory;
use App\Infrastructure\Models\InventoryReservation;
use App\Infrastructure\Models\Order;
use App\Infrastructure\Models\OrderItem;
use Illuminate\Support\Facades\DB;

final class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function create(array $orderData, array $items): array
    {
        return DB::transaction(function () use ($orderData, $items) {
            $order = Order::create($orderData);

            foreach ($items as $item) {
                $order->items()->create($item);
            }

            return $this->formatOrder($order->fresh(['items']));
        });
    }

    public function createWithReservations(array $orderData, array $items): array
    {
        return DB::transaction(function () use ($orderData, $items) {
            $order = Order::create($orderData);

            foreach ($items as $item) {
                $order->items()->create($item);

                $inventory = Inventory::where('warehouse_id', $item['warehouse_id'])
                    ->where('product_variant_id', $item['product_variant_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($inventory->quantity < $item['quantity']) {
                    throw InsufficientStockException::forVariant(
                        $item['product_variant_id'],
                        $item['warehouse_id'],
                        $item['quantity'],
                        $inventory->quantity
                    );
                }

                $inventory->decrement('quantity', $item['quantity']);

                InventoryReservation::create([
                    'inventory_id' => $inventory->id,
                    'order_id' => $order->id,
                    'product_variant_id' => $item['product_variant_id'],
                    'quantity' => $item['quantity'],
                    'status' => 'pending',
                    'expires_at' => now()->addMinutes(30),
                ]);
            }

            return $this->formatOrder($order->fresh(['items']));
        });
    }

    public function update(string $id, array $data): array
    {
        $order = Order::findOrFail($id);
        $order->update($data);

        return $this->formatOrder($order->fresh(['items']));
    }

    public function delete(string $id): bool
    {
        $order = Order::findOrFail($id);

        return $order->delete();
    }

    public function findById(string $id): ?array
    {
        $order = Order::with(['items'])->find($id);

        return $order ? $this->formatOrder($order) : null;
    }

    public function findByCode(string $code): ?array
    {
        $order = Order::with(['items'])->where('code', $code)->first();

        return $order ? $this->formatOrder($order) : null;
    }

    public function exists(string $id): bool
    {
        return Order::where('id', $id)->exists();
    }

    public function belongsToUser(string $orderId, string $userId): bool
    {
        return Order::where('id', $orderId)->where('user_id', $userId)->exists();
    }

    public function getByUserId(string $userId): array
    {
        return Order::with(['items'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn (Order $order) => $this->formatOrder($order))
            ->toArray();
    }

    public function getAll(array $filters = []): array
    {
        $query = Order::with(['items']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')
            ->get()
            ->map(fn (Order $order) => $this->formatOrder($order))
            ->toArray();
    }

    public function paginate(array $filters = [], int $perPage = 15, int $page = 1): array
    {
        $query = Order::with(['items']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $paginator = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => collect($paginator->items())
                ->map(fn (Order $order) => $this->formatOrder($order))
                ->toArray(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];
    }

    private function formatOrder(Order $order): array
    {
        return [
            'id' => $order->id,
            'code' => $order->code,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'total_amount' => $order->total_amount,
            'shipping_address' => $order->shipping_address,
            'billing_address' => $order->billing_address,
            'notes' => $order->notes,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
            'items' => $order->items->map(fn (OrderItem $item) => [
                'id' => $item->id,
                'product_variant_id' => $item->product_variant_id,
                'warehouse_id' => $item->warehouse_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'subtotal' => $item->subtotal,
                'product_snapshot' => $item->product_snapshot,
            ])->toArray(),
        ];
    }
}
