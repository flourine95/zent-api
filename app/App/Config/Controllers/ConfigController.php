<?php

namespace App\App\Config\Controllers;

use App\Domain\Config\Actions\GetAppConfigAction;
use Illuminate\Http\JsonResponse;

final readonly class ConfigController
{
    public function __construct(
        private GetAppConfigAction $getAppConfigAction
    ) {}

    public function index(): JsonResponse
    {
        $config = $this->getAppConfigAction->execute();

        return response()->json([
            'success' => true,
            'data' => $config,
        ]);
    }
}
