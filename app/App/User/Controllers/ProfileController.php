<?php

namespace App\App\User\Controllers;

use App\App\User\Requests\ChangePasswordRequest;
use App\App\User\Requests\UpdateProfileRequest;
use App\Domain\User\Actions\ChangePasswordAction;
use App\Domain\User\Actions\UpdateProfileAction;
use App\Domain\User\DataTransferObjects\UpdateProfileData;
use App\Domain\User\Exceptions\EmailAlreadyExistsException;
use App\Domain\User\Exceptions\InvalidCredentialsException;
use App\Domain\User\Exceptions\UserNotFoundException;
use Illuminate\Http\JsonResponse;

final class ProfileController
{
    public function __construct(
        private readonly UpdateProfileAction $updateProfileAction,
        private readonly ChangePasswordAction $changePasswordAction,
    ) {}

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $data = UpdateProfileData::fromArray(
                $request->user()->id,
                $request->validated()
            );

            $user = $this->updateProfileAction->execute($data);

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật thông tin',
                'data' => $user,
            ]);
        } catch (UserNotFoundException|EmailAlreadyExistsException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function updatePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $this->changePasswordAction->execute(
                $request->user()->id,
                $request->validated('current_password'),
                $request->validated('password')
            );

            return response()->json([
                'success' => true,
                'message' => 'Đã đổi mật khẩu thành công',
            ]);
        } catch (InvalidCredentialsException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => [
                    'current_password' => [$e->getMessage()],
                ],
            ], 422);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}
