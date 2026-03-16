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
use App\Shared\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

final readonly class ProfileController
{
    use ApiResponse;

    public function __construct(
        private UpdateProfileAction $updateProfileAction,
        private ChangePasswordAction $changePasswordAction,
    ) {}

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $data = UpdateProfileData::fromArray($request->user()->id, $request->validated());

            return $this->success($this->updateProfileAction->execute($data));
        } catch (EmailAlreadyExistsException|UserNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 422);
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

            return $this->message('Đã đổi mật khẩu thành công');
        } catch (InvalidCredentialsException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 422);
        } catch (UserNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        }
    }
}
