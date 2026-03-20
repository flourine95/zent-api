<?php

namespace App\App\Order\Controllers;

use App\App\Order\Requests\CreateOrderRequest;
use App\App\Order\Requests\UpdateOrderRequest;
use App\Domain\Inventory\Exceptions\InsufficientStockException;
use App\Domain\Order\Actions\CancelOrderAction;
use App\Domain\Order\Actions\CreateOrderAction;
use App\Domain\Order\Actions\UpdateOrderAction;
use App\Domain\Order\DataTransferObjects\CreateOrderData;
use App\Domain\Order\DataTransferObjects\UpdateOrderData;
use App\Domain\Order\Exceptions\InvalidOrderException;
use App\Domain\Order\Exceptions\OrderNotFoundException;
use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Shared\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final readonly class OrderController
{
    use ApiResponse;

    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private CreateOrderAction $createOrderAction,
        private UpdateOrderAction $updateOrderAction,
        private CancelOrderAction $cancelOrderAction,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'payment_status', 'date_from', 'date_to']);

        // Non-admin users only see their own orders
        if (! $request->user()?->hasRole('admin')) {
            $filters['user_id'] = $request->user()->id;
        }

        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);

        $result = $this->orderRepository->paginate($filters, $perPage, $page);

        return $this->paginated($result['data'], $result['meta']);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $order = $this->orderRepository->findById($id);

            if ($order === null) {
                throw OrderNotFoundException::withId($id);
            }

            return $this->success($order);
        } catch (OrderNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        }
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            $data = CreateOrderData::fromRequest(
                userId: $request->user()->id,
                code: 'ORD-'.strtoupper(Str::random(10)),
                validated: $request->validated()
            );

            return $this->created($this->createOrderAction->execute($data));
        } catch (InsufficientStockException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 422);
        } catch (InvalidOrderException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 422);
        }
    }

    public function update(UpdateOrderRequest $request, int $id): JsonResponse
    {
        try {
            $data = UpdateOrderData::fromArray($id, $request->validated());

            return $this->success($this->updateOrderAction->execute($data));
        } catch (OrderNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        }
    }

    public function cancel(int $id): JsonResponse
    {
        try {
            return $this->success($this->cancelOrderAction->execute($id));
        } catch (OrderNotFoundException|InvalidOrderException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 422);
        }
    }
}
