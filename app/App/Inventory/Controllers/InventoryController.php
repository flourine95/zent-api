<?php

namespace App\App\Inventory\Controllers;

use App\App\Inventory\Requests\AdjustInventoryRequest;
use App\App\Inventory\Requests\CreateInventoryRequest;
use App\App\Inventory\Requests\UpdateInventoryRequest;
use App\Domain\Inventory\Actions\AdjustInventoryAction;
use App\Domain\Inventory\Actions\CreateInventoryAction;
use App\Domain\Inventory\Actions\UpdateInventoryAction;
use App\Domain\Inventory\DataTransferObjects\CreateInventoryData;
use App\Domain\Inventory\DataTransferObjects\UpdateInventoryData;
use App\Domain\Inventory\Exceptions\DuplicateInventoryException;
use App\Domain\Inventory\Exceptions\InsufficientInventoryException;
use App\Domain\Inventory\Exceptions\InventoryNotFoundException;
use App\Domain\Inventory\Repositories\InventoryRepositoryInterface;
use App\Shared\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

final readonly class InventoryController
{
    use ApiResponse;

    public function __construct(
        private InventoryRepositoryInterface $inventoryRepository,
        private CreateInventoryAction $createInventoryAction,
        private UpdateInventoryAction $updateInventoryAction,
        private AdjustInventoryAction $adjustInventoryAction,
    ) {}

    public function index(): JsonResponse
    {
        return $this->success($this->inventoryRepository->getAll());
    }

    public function lowStock(int $threshold = 10): JsonResponse
    {
        return $this->success($this->inventoryRepository->getLowStock($threshold));
    }

    public function byWarehouse(string $warehouseId): JsonResponse
    {
        return $this->success($this->inventoryRepository->getByWarehouse($warehouseId));
    }

    public function byProductVariant(string $productVariantId): JsonResponse
    {
        return $this->success($this->inventoryRepository->getByProductVariant($productVariantId));
    }

    public function show(string $id): JsonResponse
    {
        try {
            $inventory = $this->inventoryRepository->findById($id);

            if ($inventory === null) {
                throw InventoryNotFoundException::withId($id);
            }

            return $this->success($inventory);
        } catch (InventoryNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        }
    }

    public function store(CreateInventoryRequest $request): JsonResponse
    {
        try {
            $data = CreateInventoryData::fromArray($request->validated());

            return $this->created($this->createInventoryAction->execute($data));
        } catch (DuplicateInventoryException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 422);
        }
    }

    public function update(UpdateInventoryRequest $request, string $id): JsonResponse
    {
        try {
            $data = UpdateInventoryData::fromArray($id, $request->validated());

            return $this->success($this->updateInventoryAction->execute($data));
        } catch (InventoryNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        }
    }

    public function adjust(AdjustInventoryRequest $request, string $id): JsonResponse
    {
        try {
            $inventory = $this->adjustInventoryAction->execute(
                $id,
                $request->validated('adjustment'),
                $request->validated('reason')
            );

            return $this->success($inventory);
        } catch (InventoryNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        } catch (InsufficientInventoryException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 422);
        }
    }
}
