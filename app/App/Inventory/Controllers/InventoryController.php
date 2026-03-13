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
use Illuminate\Http\JsonResponse;

final readonly class InventoryController
{
    public function __construct(
        private InventoryRepositoryInterface $inventoryRepository,
        private CreateInventoryAction $createInventoryAction,
        private UpdateInventoryAction $updateInventoryAction,
        private AdjustInventoryAction $adjustInventoryAction,
    ) {}

    public function index(): JsonResponse
    {
        $inventories = $this->inventoryRepository->getAll();

        return response()->json(['data' => $inventories]);
    }

    public function lowStock(int $threshold = 10): JsonResponse
    {
        $inventories = $this->inventoryRepository->getLowStock($threshold);

        return response()->json(['data' => $inventories]);
    }

    public function byWarehouse(int $warehouseId): JsonResponse
    {
        $inventories = $this->inventoryRepository->getByWarehouse($warehouseId);

        return response()->json(['data' => $inventories]);
    }

    public function byProductVariant(int $productVariantId): JsonResponse
    {
        $inventories = $this->inventoryRepository->getByProductVariant($productVariantId);

        return response()->json(['data' => $inventories]);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $inventory = $this->inventoryRepository->findById($id);

            if ($inventory === null) {
                throw InventoryNotFoundException::withId($id);
            }

            return response()->json(['data' => $inventory]);
        } catch (InventoryNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function store(CreateInventoryRequest $request): JsonResponse
    {
        try {
            $data = CreateInventoryData::fromArray($request->validated());
            $inventory = $this->createInventoryAction->execute($data);

            return response()->json(['data' => $inventory], 201);
        } catch (DuplicateInventoryException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function update(UpdateInventoryRequest $request, int $id): JsonResponse
    {
        try {
            $data = UpdateInventoryData::fromArray($id, $request->validated());
            $inventory = $this->updateInventoryAction->execute($data);

            return response()->json(['data' => $inventory]);
        } catch (InventoryNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function adjust(AdjustInventoryRequest $request, int $id): JsonResponse
    {
        try {
            $inventory = $this->adjustInventoryAction->execute(
                $id,
                $request->validated('adjustment'),
                $request->validated('reason')
            );

            return response()->json(['data' => $inventory]);
        } catch (InventoryNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (InsufficientInventoryException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
