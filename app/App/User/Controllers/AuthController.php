<?php

namespace App\App\User\Controllers;

use App\App\User\Requests\LoginRequest;
use App\App\User\Requests\RegisterRequest;
use App\Domain\User\Actions\LoginUserAction;
use App\Domain\User\Actions\RegisterUserAction;
use App\Domain\User\DataTransferObjects\LoginUserData;
use App\Domain\User\DataTransferObjects\RegisterUserData;
use App\Domain\User\Exceptions\EmailAlreadyExistsException;
use App\Domain\User\Exceptions\InvalidCredentialsException;
use App\Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AuthController
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RegisterUserAction $registerUserAction,
        private readonly LoginUserAction $loginUserAction,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $data = RegisterUserData::fromArray($request->validated());
            $user = $this->registerUserAction->execute($data);

            // Create token
            $token = $this->userRepository->createToken($user['id'], 'auth-token');

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
            ], 201);
        } catch (EmailAlreadyExistsException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = LoginUserData::fromArray($request->validated());
            $user = $this->loginUserAction->execute($data);

            // Create token
            $token = $this->userRepository->createToken($user['id'], 'auth-token');

            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
            ]);
        } catch (InvalidCredentialsException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $tokenId = $request->user()->currentAccessToken()->id;

        $this->userRepository->revokeToken($userId, $tokenId);

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->userRepository->findById($request->user()->id);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
            ],
        ]);
    }
}
