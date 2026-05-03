<?php

namespace App\DTO\Auth;

use App\Models\User;

final readonly class AuthenticatedUserDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
    ) {
    }

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
        );
    }
}
