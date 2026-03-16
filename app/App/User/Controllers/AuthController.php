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
use App\Shared\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class AuthController
{
    use ApiResponse;

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RegisterUserAction $registerUserAction,
        private LoginUserAction $loginUserAction,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $data = RegisterUserData::fromArray($request->validated());
            $user = $this->registerUserAction->execute($data);
            $token = $this->userRepository->createToken($user['id'], 'auth-token');

            return $this->created(['user' => $user, 'token' => $token], 'Đăng ký thành công');
        } catch (EmailAlreadyExistsException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 422);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = LoginUserData::fromArray($request->validated());
            $user = $this->loginUserAction->execute($data);
            $token = $this->userRepository->createToken($user['id'], 'auth-token');

            return $this->success(['user' => $user, 'token' => $token]);
        } catch (InvalidCredentialsException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $this->userRepository->revokeToken(
            $request->user()->id,
            $request->user()->currentAccessToken()->id
        );

        return $this->message('Đăng xuất thành công');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success($this->userRepository->findById($request->user()->id));
    }
}
