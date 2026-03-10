<?php

namespace App\Domain\Config\Actions;

use App\Domain\Config\Repositories\ConfigRepositoryInterface;

final readonly class GetAppConfigAction
{
    public function __construct(
        private ConfigRepositoryInterface $configRepository
    ) {}

    public function execute(): array
    {
        return $this->configRepository->getAppConfig();
    }
}
