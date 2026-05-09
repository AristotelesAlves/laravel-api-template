<?php

namespace App\DTO\Auth;

final readonly class RegisterInputDTO
{
    /**
     * @param  string  $email
     * @param  string  $password
     * @param  string  $confirmPassword
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $confirmPassword,
    ) {}
}
