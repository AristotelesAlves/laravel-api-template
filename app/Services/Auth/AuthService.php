<?php

namespace App\Services\Auth;

use App\DTO\Auth\AuthenticatedUserDTO;
use App\DTO\Auth\LoginInputDTO;
use App\DTO\Auth\LoginOutputDTO;
use App\DTO\Auth\RegisterInputDTO;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Summary of __construct
     * @param UserRepository $users
     */
    public function __construct(private readonly UserRepository $users) {}

    /**
     * Summary of login
     * @param LoginInputDTO $input
     * @return LoginOutputDTO
     */
    public function login(LoginInputDTO $input): LoginOutputDTO
    {
        $user = $this->users->findByEmail($input->email);

        if (!Hash::check($input->password, (string) $user->password)) {
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

    function register(RegisterInputDTO $input): void
    {
        $this->users->create([
            'name' => $input->name,
            'email' => $input->email,
            'password' => Hash::make($input->password),
        ]);
    }

    /**
     * Summary of logout
     * @param mixed $currentToken
     * @return void
     */
    public function logout(?object $currentToken): void
    {
        if ($currentToken !== null && method_exists($currentToken, 'delete')) {
            $currentToken->delete();
        }
    }

    /**
     * Summary of authenticatedUser
     * @param User $user
     * @return AuthenticatedUserDTO
     */
    public function authenticatedUser(User $user): AuthenticatedUserDTO
    {
        return AuthenticatedUserDTO::fromModel($user);
    }
}
