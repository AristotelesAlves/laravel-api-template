<?php

namespace App\DTO\Auth;

final readonly class LoginInputDTO
{
    /**
     * @param  string  $email
     * @param  string  $password
     * @param  string  $deviceName
     */
    public function __construct(
        public string $email,
        public string $password,
        public string $deviceName = 'api-token',
    ) {}
}
