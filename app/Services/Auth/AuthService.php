<?php

namespace App\Services\Auth;

use App\DTO\Auth\AuthenticatedUserDTO;
use App\DTO\Auth\LoginInputDTO;
use App\DTO\Auth\LoginOutputDTO;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(private readonly UserRepository $users)
    {}

    public function login(LoginInputDTO $input): LoginOutputDTO
    {
        $user = $this->users->findByEmail($input->email);

        if ($user === null || !Hash::check($input->password, (string) $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return new LoginOutputDTO(
            token: $user->createToken($input->deviceName)->plainTextToken,
            tokenType: 'Bearer',
            user: AuthenticatedUserDTO::fromModel($user),
        );
    }

    public function logout(?object $currentToken): void
    {
        if ($currentToken !== null && method_exists($currentToken, 'delete')) {
            $currentToken->delete();
        }
    }

    public function authenticatedUser(User $user): AuthenticatedUserDTO
    {
        return AuthenticatedUserDTO::fromModel($user);
    }
}
