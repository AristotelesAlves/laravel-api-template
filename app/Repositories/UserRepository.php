<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * Summary of findByEmail
     * @param string $email
     * @return User|\stdClass|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }
}
