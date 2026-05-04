<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthController extends Controller
{
    /**
     * Summary of login
     * @param LoginRequest $request
     * @param AuthService $auth
     * @return LoginResource
     */
    public function login(LoginRequest $request, AuthService $auth): LoginResource
    {
        return new LoginResource($auth->login($request->toDTO()));
    }

    /**
     * Summary of logout
     * @param Request $request
     * @param AuthService $auth
     * @return JsonResponse
     */
    public function logout(Request $request, AuthService $auth): JsonResponse
    {
        $user = $request->user();

        $auth->logout($user instanceof User ? $user->currentAccessToken() : null);

        return new JsonResponse([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Summary of me
     * @param Request $request
     * @param AuthService $auth
     * @throws UnauthorizedHttpException
     * @return UserResource
     */
    public function me(Request $request, AuthService $auth): UserResource
    {
        $user = $request->user();

        if (! $user instanceof User) {
            throw new UnauthorizedHttpException('', 'Unauthenticated.');
        }

        return new UserResource($auth->authenticatedUser($user));
    }

    /**
     * Summary of protectedTest
     * @return JsonResponse
     */
    public function protectedTest(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Authenticated request successful.',
        ]);
    }
}
