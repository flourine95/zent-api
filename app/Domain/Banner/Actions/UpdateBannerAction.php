<?php

namespace App\Domain\Banner\Actions;

use App\Domain\Banner\DataTransferObjects\UpdateBannerData;
use App\Domain\Banner\Exceptions\BannerNotFoundException;
use App\Domain\Banner\Exceptions\InvalidBannerException;
use App\Domain\Banner\Repositories\BannerRepositoryInterface;

final readonly class UpdateBannerAction
{
    public function __construct(
        private BannerRepositoryInterface $bannerRepository
    ) {}

    /**
     * @throws BannerNotFoundException
     * @throws InvalidBannerException
     */
    public function execute(UpdateBannerData $data): array
    {
        if (! $this->bannerRepository->exists($data->id)) {
            throw BannerNotFoundException::withId($data->id);
        }

        // Validate date range
        if ($data->startDate && $data->endDate) {
            if ($data->startDate > $data->endDate) {
                throw InvalidBannerException::invalidDateRange();
            }
        }

        return $this->bannerRepository->update($data->id, $data->toArray());
    }
}
