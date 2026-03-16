<?php

namespace App\Shared\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success(mixed $data, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    protected function paginated(array $data, array $meta): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => $meta,
        ]);
    }

    protected function message(string $message, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], $status);
    }

    protected function created(mixed $data, string $message = ''): JsonResponse
    {
        $body = ['success' => true, 'data' => $data];

        if ($message !== '') {
            $body['message'] = $message;
        }

        return response()->json($body, 201);
    }

    protected function error(string $message, string $code, int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code' => $code,
            'message' => $message,
        ], $status);
    }
}
