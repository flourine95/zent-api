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
use App\Shared\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class AddressController
{
    use ApiResponse;

    public function __construct(
        private GetUserAddressesAction $getUserAddressesAction,
        private CreateAddressAction $createAddressAction,
        private UpdateAddressAction $updateAddressAction,
        private DeleteAddressAction $deleteAddressAction,
        private SetDefaultAddressAction $setDefaultAddressAction,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $addresses = $this->getUserAddressesAction->execute($request->user()->id);

        return $this->success($addresses);
    }

    public function store(CreateAddressRequest $request): JsonResponse
    {
        $data = CreateAddressData::fromArray([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        $address = $this->createAddressAction->execute($data);

        return $this->created($address, 'Address created successfully');
    }

    public function update(UpdateAddressRequest $request, string $address): JsonResponse
    {
        try {
            $data = UpdateAddressData::fromArray([
                ...$request->validated(),
                'id' => $address,
                'user_id' => $request->user()->id,
            ]);

            return $this->success($this->updateAddressAction->execute($data));
        } catch (AddressNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        } catch (UnauthorizedAddressAccessException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 403);
        }
    }

    public function destroy(Request $request, string $address): JsonResponse
    {
        try {
            $this->deleteAddressAction->execute($request->user()->id, $address);

            return $this->message('Address deleted successfully');
        } catch (AddressNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        } catch (UnauthorizedAddressAccessException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 403);
        }
    }

    public function setDefault(Request $request, string $address): JsonResponse
    {
        try {
            $updated = $this->setDefaultAddressAction->execute($request->user()->id, $address);

            return $this->success($updated);
        } catch (AddressNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        } catch (UnauthorizedAddressAccessException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 403);
        }
    }
}
