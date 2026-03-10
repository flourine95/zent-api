<?php

namespace App\Domain\Banner\Actions;

use App\Domain\Banner\DataTransferObjects\CreateBannerData;
use App\Domain\Banner\Exceptions\InvalidBannerException;
use App\Domain\Banner\Repositories\BannerRepositoryInterface;

final readonly class CreateBannerAction
{
    public function __construct(
        private BannerRepositoryInterface $bannerRepository
    ) {}

    /**
     * @throws InvalidBannerException
     */
    public function execute(CreateBannerData $data): array
    {
        // Validate date range
        if ($data->startDate && $data->endDate) {
            if ($data->startDate > $data->endDate) {
                throw InvalidBannerException::invalidDateRange();
            }
        }

        return $this->bannerRepository->create($data->toArray());
    }
}
