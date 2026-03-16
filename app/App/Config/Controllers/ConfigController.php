<?php

namespace App\App\Config\Controllers;

use App\Domain\Config\Actions\GetAppConfigAction;
use App\Shared\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

final readonly class ConfigController
{
    use ApiResponse;

    public function __construct(
        private GetAppConfigAction $getAppConfigAction,
    ) {}

    public function index(): JsonResponse
    {
        return $this->success($this->getAppConfigAction->execute());
    }
}
