<?php

namespace App\App\Shipping\Controllers;

use App\App\Shipping\Requests\CreateShipmentRequest;
use App\Domain\Order\Exceptions\OrderNotFoundException;
use App\Domain\Order\Exceptions\UnauthorizedOrderAccessException;
use App\Domain\Shipping\Actions\CancelShipmentAction;
use App\Domain\Shipping\Actions\CreateShipmentAction;
use App\Domain\Shipping\Actions\GetShipmentAction;
use App\Domain\Shipping\DataTransferObjects\CreateShipmentData;
use App\Domain\Shipping\Exceptions\ShipmentAlreadyExistsException;
use App\Domain\Shipping\Exceptions\ShipmentCancellationException;
use App\Domain\Shipping\Exceptions\ShipmentNotFoundException;
use App\Shared\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class ShipmentController
{
    use ApiResponse;

    public function __construct(
        private CreateShipmentAction $createShipmentAction,
        private GetShipmentAction $getShipmentAction,
        private CancelShipmentAction $cancelShipmentAction,
    ) {}

    public function store(CreateShipmentRequest $request, string $orderId): JsonResponse
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

    public function show(Request $request, string $orderId): JsonResponse
    {
        try {
            $isAdmin = $request->user()?->hasRole('admin') ?? false;

            return $this->success($this->getShipmentAction->execute($orderId, $request->user()->id, $isAdmin));
        } catch (OrderNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        } catch (UnauthorizedOrderAccessException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 403);
        } catch (ShipmentNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        }
    }

    public function cancel(Request $request, string $orderId): JsonResponse
    {
        try {
            $isAdmin = $request->user()?->hasRole('admin') ?? false;

            return $this->success($this->cancelShipmentAction->execute($orderId, $request->user()->id, $isAdmin));
        } catch (OrderNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        } catch (UnauthorizedOrderAccessException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 403);
        } catch (ShipmentNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        } catch (ShipmentCancellationException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 422);
        }
    }
}
