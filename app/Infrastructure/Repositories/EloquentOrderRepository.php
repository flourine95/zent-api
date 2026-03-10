<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Infrastructure\Models\Order;
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

            return $order->load(['items', 'user'])->toArray();
        });
    }

    public function update(int $id, array $data): array
    {
        $order = Order::findOrFail($id);
        $order->update($data);

        return $order->fresh(['items', 'user'])->toArray();
    }

    public function delete(int $id): bool
    {
        $order = Order::findOrFail($id);

        return $order->delete();
    }

    public function findById(int $id): ?array
    {
        $order = Order::with(['items', 'user', 'inventoryReservations'])->find($id);

        return $order?->toArray();
    }

    public function findByCode(string $code): ?array
    {
        $order = Order::with(['items', 'user'])->where('code', $code)->first();

        return $order?->toArray();
    }

    public function exists(int $id): bool
    {
        return Order::where('id', $id)->exists();
    }

    public function getByUserId(int $userId): array
    {
        return Order::with(['items'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function getAll(array $filters = []): array
    {
        $query = Order::with(['items', 'user']);

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

        return $query->orderBy('created_at', 'desc')->get()->toArray();
    }
}
