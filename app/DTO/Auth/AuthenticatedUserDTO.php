<?php

namespace App\DTO\Auth;

use App\Models\User;

final readonly class AuthenticatedUserDTO
{
    /**
     * @param  string  $id
     * @param  string  $name
     * @param  string  $email
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
    ) {}

    /**
     * Create an authenticated user DTO from a user model.
     */
    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
        );
    }
}
