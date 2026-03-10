<?php

namespace App\Domain\Banner\Actions;

use App\Domain\Banner\Exceptions\BannerNotFoundException;
use App\Domain\Banner\Repositories\BannerRepositoryInterface;

final readonly class DeleteBannerAction
{
    public function __construct(
        private BannerRepositoryInterface $bannerRepository
    ) {}

    /**
     * @throws BannerNotFoundException
     */
    public function execute(int $bannerId): bool
    {
        if (! $this->bannerRepository->exists($bannerId)) {
            throw BannerNotFoundException::withId($bannerId);
        }

        return $this->bannerRepository->delete($bannerId);
    }
}
