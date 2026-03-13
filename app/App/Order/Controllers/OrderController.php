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
use Illuminate\Http\JsonResponse;

final readonly class OrderController
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private CreateOrderAction $createOrderAction,
        private UpdateOrderAction $updateOrderAction,
        private CancelOrderAction $cancelOrderAction,
    ) {}

    public function index(): JsonResponse
    {
        $orders = $this->orderRepository->getAll();

        return response()->json(['data' => $orders]);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $order = $this->orderRepository->findById($id);

            if ($order === null) {
                throw OrderNotFoundException::withId($id);
            }

            return response()->json(['data' => $order]);
        } catch (OrderNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            $data = CreateOrderData::fromArray($request->validated());
            $order = $this->createOrderAction->execute($data);

            return response()->json(['data' => $order], 201);
        } catch (InvalidOrderException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function update(UpdateOrderRequest $request, int $id): JsonResponse
    {
        try {
            $data = UpdateOrderData::fromArray($id, $request->validated());
            $order = $this->updateOrderAction->execute($data);

            return response()->json(['data' => $order]);
        } catch (OrderNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function cancel(int $id): JsonResponse
    {
        try {
            $order = $this->cancelOrderAction->execute($id);

            return response()->json(['data' => $order]);
        } catch (OrderNotFoundException|InvalidOrderException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
