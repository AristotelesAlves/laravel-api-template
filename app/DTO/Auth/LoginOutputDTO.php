<?php

namespace App\DTO\Auth;

final readonly class LoginOutputDTO
{
    public function __construct(
        public string $token,
        public string $tokenType,
        public AuthenticatedUserDTO $user,
    ) {
    }
}
