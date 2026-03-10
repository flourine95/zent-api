<?php

namespace App\App\Address\Controllers;

use App\App\Address\Requests\CreateAddressRequest;
use App\App\Address\Requests\UpdateAddressRequest;
use App\Domain\Address\Actions\CreateAddressAction;
use App\Domain\Address\Actions\DeleteAddressAction;
use App\Domain\Address\Actions\GetUserAddressesAction;
use App\Domain\Address\Actions\SetDefaultAddressAction;
use App\Domain\Address\Actions\UpdateAddressAction;
use App\Domain\Address\DataTransferObjects\CreateAddressData;
use App\Domain\Address\DataTransferObjects\UpdateAddressData;
use App\Domain\Address\Exceptions\AddressNotFoundException;
use App\Domain\Address\Exceptions\UnauthorizedAddressAccessException;
use App\Http\Resources\Api\AddressResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AddressController
{
    public function __construct(
        private readonly GetUserAddressesAction $getUserAddressesAction,
        private readonly CreateAddressAction $createAddressAction,
        private readonly UpdateAddressAction $updateAddressAction,
        private readonly DeleteAddressAction $deleteAddressAction,
        private readonly SetDefaultAddressAction $setDefaultAddressAction,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $addresses = $this->getUserAddressesAction->execute($request->user()->id);

        return response()->json([
            'success' => true,
            'data' => AddressResource::collection(collect($addresses)),
        ]);
    }

    public function store(CreateAddressRequest $request): JsonResponse
    {
        $data = CreateAddressData::fromArray([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        $address = $this->createAddressAction->execute($data);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm địa chỉ mới',
            'data' => new AddressResource((object) $address),
        ], 201);
    }

    public function update(UpdateAddressRequest $request, int $address): JsonResponse
    {
        try {
            $data = UpdateAddressData::fromArray([
                ...$request->validated(),
                'id' => $address,
                'user_id' => $request->user()->id,
            ]);

            $updatedAddress = $this->updateAddressAction->execute($data);

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật địa chỉ',
                'data' => new AddressResource((object) $updatedAddress),
            ]);
        } catch (AddressNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } catch (UnauthorizedAddressAccessException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền cập nhật địa chỉ này',
            ], 403);
        }
    }

    public function destroy(Request $request, int $address): JsonResponse
    {
        try {
            $this->deleteAddressAction->execute($request->user()->id, $address);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa địa chỉ',
            ]);
        } catch (AddressNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } catch (UnauthorizedAddressAccessException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền xóa địa chỉ này',
            ], 403);
        }
    }

    public function setDefault(Request $request, int $address): JsonResponse
    {
        try {
            $updatedAddress = $this->setDefaultAddressAction->execute($request->user()->id, $address);

            return response()->json([
                'success' => true,
                'message' => 'Đã đặt làm địa chỉ mặc định',
                'data' => new AddressResource((object) $updatedAddress),
            ]);
        } catch (AddressNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } catch (UnauthorizedAddressAccessException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền thay đổi địa chỉ này',
            ], 403);
        }
    }
}
