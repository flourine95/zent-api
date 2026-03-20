<?php

namespace App\App\Shipping\Controllers;

use App\App\Shipping\Requests\CreateShipmentRequest;
use App\Domain\Order\Exceptions\OrderNotFoundException;
use App\Domain\Shipping\Actions\CancelShipmentAction;
use App\Domain\Shipping\Actions\CreateShipmentAction;
use App\Domain\Shipping\Actions\GetShipmentAction;
use App\Domain\Shipping\DataTransferObjects\CreateShipmentData;
use App\Domain\Shipping\Exceptions\ShipmentAlreadyExistsException;
use App\Domain\Shipping\Exceptions\ShipmentCancellationException;
use App\Domain\Shipping\Exceptions\ShipmentNotFoundException;
use App\Shared\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

final readonly class ShipmentController
{
    use ApiResponse;

    public function __construct(
        private CreateShipmentAction $createShipmentAction,
        private GetShipmentAction $getShipmentAction,
        private CancelShipmentAction $cancelShipmentAction,
    ) {}

    public function store(CreateShipmentRequest $request, int $orderId): JsonResponse
    {
        try {
            $data = CreateShipmentData::fromArray($orderId, $request->validated());

            return $this->created($this->createShipmentAction->execute($data));
        } catch (OrderNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        } catch (ShipmentAlreadyExistsException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 409);
        }
    }

    public function show(int $orderId): JsonResponse
    {
        try {
            return $this->success($this->getShipmentAction->execute($orderId));
        } catch (ShipmentNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        }
    }

    public function cancel(int $orderId): JsonResponse
    {
        try {
            return $this->success($this->cancelShipmentAction->execute($orderId));
        } catch (ShipmentNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        } catch (ShipmentCancellationException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 422);
        }
    }
}
