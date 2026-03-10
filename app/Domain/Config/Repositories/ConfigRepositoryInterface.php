<?php

namespace App\Domain\Config\Repositories;

interface ConfigRepositoryInterface
{
    public function getAppConfig(): array;
}
