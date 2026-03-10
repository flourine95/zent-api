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
use Illuminate\Http\JsonResponse;

final class BannerController
{
    public function __construct(
        private readonly BannerRepositoryInterface $bannerRepository,
        private readonly CreateBannerAction $createBannerAction,
        private readonly UpdateBannerAction $updateBannerAction,
        private readonly DeleteBannerAction $deleteBannerAction,
    ) {}

    public function index(): JsonResponse
    {
        $banners = $this->bannerRepository->getAll();

        return response()->json(['data' => $banners]);
    }

    public function active(): JsonResponse
    {
        $banners = $this->bannerRepository->getActive();

        return response()->json(['data' => $banners]);
    }

    public function byPosition(string $position): JsonResponse
    {
        $banners = $this->bannerRepository->getByPosition($position);

        return response()->json(['data' => $banners]);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $banner = $this->bannerRepository->findById($id);

            if ($banner === null) {
                throw BannerNotFoundException::withId($id);
            }

            return response()->json(['data' => $banner]);
        } catch (BannerNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function store(CreateBannerRequest $request): JsonResponse
    {
        try {
            $data = CreateBannerData::fromArray($request->validated());
            $banner = $this->createBannerAction->execute($data);

            return response()->json(['data' => $banner], 201);
        } catch (InvalidBannerException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function update(UpdateBannerRequest $request, int $id): JsonResponse
    {
        try {
            $data = UpdateBannerData::fromArray($id, $request->validated());
            $banner = $this->updateBannerAction->execute($data);

            return response()->json(['data' => $banner]);
        } catch (BannerNotFoundException|InvalidBannerException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->deleteBannerAction->execute($id);

            return response()->json(['message' => 'Banner deleted successfully']);
        } catch (BannerNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
