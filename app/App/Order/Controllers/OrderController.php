<?php

namespace App\App\Order\Controllers;

use App\App\Order\Requests\CreateOrderRequest;
use App\App\Order\Requests\UpdateOrderRequest;
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

final readonly class OrderController
{
    use ApiResponse;

    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private CreateOrderAction $createOrderAction,
        private UpdateOrderAction $updateOrderAction,
        private CancelOrderAction $cancelOrderAction,
    ) {}

    public function index(): JsonResponse
    {
        return $this->success($this->orderRepository->getAll());
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
            $data = CreateOrderData::fromArray($request->validated());

            return $this->created($this->createOrderAction->execute($data));
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
