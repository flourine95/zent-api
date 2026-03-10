<?php

namespace App\Domain\User\DataTransferObjects;

final readonly class UpdateProfileData
{
    public function __construct(
        public int $userId,
        public string $name,
        public string $email,
    ) {}

    public static function fromArray(int $userId, array $data): self
    {
        return new self(
            userId: $userId,
            name: $data['name'],
            email: $data['email'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
