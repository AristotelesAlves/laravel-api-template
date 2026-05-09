<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Template Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'confirmPassword' => 'password',
        ])
            ->assertCreated()
            ->assertJsonPath('message', 'Registration successful. Please log in.');

        $this->assertDatabaseHas('users', [
            'name' => 'Template Admin',
            'email' => 'admin@example.com',
        ]);
    }

    /**
     * Summary of test_user_can_login_with_valid_credentials
     * @return void
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::query()->create([
            'name' => 'Template Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.id', $user->id)
            ->assertJsonPath('data.user.email', 'admin@example.com')
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'token_type',
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                ],
            ]);
    }

    /**
     * Summary of test_user_cannot_login_with_invalid_credentials
     * @return void
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::query()->create([
            'name' => 'Template Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ])->assertUnprocessable();
    }

    /**
     * Summary of test_me_requires_authentication
     * @return void
     */
    public function test_me_requires_authentication(): void
    {
        $this->getJson('/api/me')->assertUnauthorized();
    }

    /**
     * Summary of test_authenticated_user_can_read_me
     * @return void
     */
    public function test_authenticated_user_can_read_me(): void
    {
        $user = User::query()->create([
            'name' => 'Template Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', 'admin@example.com');
    }

    /**
     * Summary of test_authenticated_user_can_logout
     * @return void
     */
    public function test_authenticated_user_can_logout(): void
    {
        $user = User::query()->create([
            'name' => 'Template Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        Sanctum::actingAs($user);

        $this->postJson('/api/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Logged out successfully.');
    }

    /**
     * Summary of test_authenticated_user_can_access_protected_test_route
     * @return void
     */
    public function test_authenticated_user_can_access_protected_test_route(): void
    {
        $user = User::query()->create([
            'name' => 'Template Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/protected-test')
            ->assertOk()
            ->assertJsonPath('message', 'Authenticated request successful.');
    }
}
