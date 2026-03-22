<?php

namespace App\App\Banner\Controllers;

use App\App\Banner\Requests\CreateBannerRequest;
use App\App\Banner\Requests\UpdateBannerRequest;
use App\Domain\Banner\Actions\CreateBannerAction;
use App\Domain\Banner\Actions\DeleteBannerAction;
use App\Domain\Banner\Actions\UpdateBannerAction;
use App\Domain\Banner\DataTransferObjects\CreateBannerData;
use App\Domain\Banner\DataTransferObjects\UpdateBannerData;
use App\Domain\Banner\Exceptions\BannerNotFoundException;
use App\Domain\Banner\Exceptions\InvalidBannerException;
use App\Domain\Banner\Repositories\BannerRepositoryInterface;
use App\Shared\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

final readonly class BannerController
{
    use ApiResponse;

    public function __construct(
        private BannerRepositoryInterface $bannerRepository,
        private CreateBannerAction $createBannerAction,
        private UpdateBannerAction $updateBannerAction,
        private DeleteBannerAction $deleteBannerAction,
    ) {}

    public function index(): JsonResponse
    {
        return $this->success($this->bannerRepository->getAll());
    }

    public function active(): JsonResponse
    {
        return $this->success($this->bannerRepository->getActive());
    }

    public function byPosition(string $position): JsonResponse
    {
        return $this->success($this->bannerRepository->getByPosition($position));
    }

    public function show(string $id): JsonResponse
    {
        try {
            $banner = $this->bannerRepository->findById($id);

            if ($banner === null) {
                throw BannerNotFoundException::withId($id);
            }

            return $this->success($banner);
        } catch (BannerNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        }
    }

    public function store(CreateBannerRequest $request): JsonResponse
    {
        try {
            $data = CreateBannerData::fromArray($request->validated());

            return $this->created($this->createBannerAction->execute($data));
        } catch (InvalidBannerException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 422);
        }
    }

    public function update(UpdateBannerRequest $request, string $id): JsonResponse
    {
        try {
            $data = UpdateBannerData::fromArray($id, $request->validated());

            return $this->success($this->updateBannerAction->execute($data));
        } catch (BannerNotFoundException|InvalidBannerException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 422);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->deleteBannerAction->execute($id);

            return $this->message('Banner deleted successfully');
        } catch (BannerNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        }
    }
}
