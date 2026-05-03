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

class AuthController extends Controller
{
    public function login(LoginRequest $request, AuthService $auth): LoginResource
    {
        return new LoginResource($auth->login($request->toDTO()));
    }

    public function logout(Request $request, AuthService $auth): JsonResponse
    {
        $auth->logout($request->user()?->currentAccessToken());

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me(Request $request, AuthService $auth): UserResource
    {
        $user = $request->user();

        abort_unless($user instanceof User, 401);

        return new UserResource($auth->authenticatedUser($user));
    }

    public function protectedTest(): JsonResponse
    {
        return response()->json([
            'message' => 'Authenticated request successful.',
        ]);
    }
}
