<?php

namespace App\DTO\Auth;

final readonly class LoginOutputDTO
{
    /**
     * @param  string                $token
     * @param  string                $tokenType
     * @param  AuthenticatedUserDTO  $user
     */
    public function __construct(
        public string $token,
        public string $tokenType,
        public AuthenticatedUserDTO $user,
    ) {}
}
