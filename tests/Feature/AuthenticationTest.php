<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('Authentication API', function () {
    describe('Login', function () {
        it('authenticates user with valid credentials', function () {
            $user = User::factory()->create([
                'email' => 'test@example.com',
                'password' => bcrypt('password123'),
            ]);

            $response = $this->postJson('/api/auth/login', [
                'email' => 'test@example.com',
                'password' => 'password123',
            ]);

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'user' => [
                        'data' => [
                            'id',
                            'name',
                            'email',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                    'token',
                ]);
        });

        it('rejects invalid credentials', function () {
            $user = User::factory()->create([
                'email' => 'test@example.com',
                'password' => bcrypt('password123'),
            ]);

            $response = $this->postJson('/api/auth/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });

        it('validates required fields', function () {
            $response = $this->postJson('/api/auth/login', []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email', 'password']);
        });
    });

    describe('Logout', function () {
        it('logs out authenticated user', function () {
            $user = User::factory()->create();
            Sanctum::actingAs($user);

            $response = $this->postJson('/api/auth/logout');

            $response->assertStatus(200)
                ->assertJson(['message' => 'Logged out successfully']);
        });

        it('requires authentication', function () {
            $response = $this->postJson('/api/auth/logout');

            $response->assertStatus(401);
        });
    });

    describe('Get User', function () {
        it('returns authenticated user data', function () {
            $user = User::factory()->create();
            Sanctum::actingAs($user);

            $response = $this->getJson('/api/auth/user');

            $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                ]);
        });

        it('requires authentication', function () {
            $response = $this->getJson('/api/auth/user');

            $response->assertStatus(401);
        });
    });

    describe('Refresh Token', function () {
        it('refreshes token for authenticated user', function () {
            $user = User::factory()->create();
            Sanctum::actingAs($user);

            $response = $this->postJson('/api/auth/refresh');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'user' => [
                        'data' => [
                            'id',
                            'name',
                            'email',
                        ],
                    ],
                    'token',
                ]);
        });

        it('requires authentication', function () {
            $response = $this->postJson('/api/auth/refresh');

            $response->assertStatus(401);
        });
    });
});
